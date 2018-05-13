<?php
namespace Framework\Migrations;

use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class Migrator
{
    const CREATE_MIGRATION_FILE_PHP = <<<'PHP'
<?php
namespace %s;

use \Framework\Migrations\AbstractMigration;

class Migration%s extends AbstractMigration
{
    public function run()
    {
        return $this->pdo->query(<<<'SQL'
--- Your migration SQL code here ---
SQL
        );
    }
}

PHP;


    protected $pdo;
    protected $schemaDriver;
    protected $migrationsFolder;
    protected $migrationsNamespace;
    /* @var $output OutputInterface */
    protected $output;

    public function __construct(\PDO $pdo, DriverInterface $schemaDriver, $migrationsFolder, $migrationsNamespace)
    {
        $this->pdo = $pdo;
        $this->schemaDriver = $schemaDriver;
        $this->migrationsFolder = $migrationsFolder;
        $this->migrationsNamespace = $migrationsNamespace;
    }

    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function getOutput()
    {
        if (is_null($this->output)) {
            $this->output = new ConsoleOutput();
        }

        return $this->output;
    }

    public function isSchemaInitialized()
    {
        $statement = $this->pdo->query($this->schemaDriver->getSchemaMigrationsTableSql());
        if ($statement) {
            $rows = $statement->fetchAll();
            return count($rows) > 0;
        }
        return false;
    }

    public function initSchema()
    {
        if (!$this->isSchemaInitialized()) {
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            try {
                $this->pdo->query($this->schemaDriver->getCreateMigrationsTableSql());

                $this->getOutput()->writeln("Schema initialized");

                return true;
            } catch (\PDOException $e) {
                throw $e;
            }
        } else {
            $this->getOutput()->writeln('Schema already initialized');
            return false;
        }
    }

    public function migrateSchema()
    {
        if ($this->isSchemaInitialized()) {
            $appliedMigrations = $this->getAppliedMigrations();

            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $counter = 0;
            foreach ($this->getSourceMigrations() as $migrationId) {
                if (!in_array($migrationId, $appliedMigrations)) {
                    $counter++;
                    try {
                        $migrationClassName = $this->migrationsNamespace . '\\' . 'Migration' . $migrationId;
                        /* @var $migration \Framework\Migrations\AbstractMigration */
                        $migration = new $migrationClassName($this->pdo);
                        $migration->run();

                        $this->pdo
                            ->prepare($this->schemaDriver->getInsertMigrationIdSql())
                            ->execute(['id' => $migrationId]);

                        $this->getOutput()->writeln('Migration'.$migrationId.' done');
                    } catch (\PDOException $e) {
                        $this->getOutput()->writeln('Migration'.$migrationId.' error');

                        throw new MigrationFailureException($e->getMessage());
                    }
                }
            }
            $this->getOutput()->writeln($counter > 0 ? 'Done' : 'No new migrations found');
        } else {
            $this->getOutput()->writeln('Schema not initialized. Run init first');
        }
    }

    protected function getSourceMigrations()
    {
        if (!is_dir($this->migrationsFolder)) {
            throw new \RuntimeException('Folder ' . $this->migrationsFolder . ' is not exists');
        }

        $files = scandir($this->migrationsFolder);

        sort($files);

        $migrations = array_filter(array_map(function ($file) {
            if (preg_match('#^Migration(\d+)\.php$#', $file, $matches)) {
                return $matches[1];
            } else {
                return null;
            }
        }, $files));

        return $migrations;
    }

    protected function getAppliedMigrations()
    {
        $statement = $this->pdo->query($this->schemaDriver->getFetchMigrationIdsSql());

        $migrations = array_map(function($row){ return $row['id']; }, $statement->fetchAll(\PDO::FETCH_ASSOC));

        return $migrations;
    }

    public function createMigrationFile()
    {
        $migrationId = gmdate('YmdHis');

        $migrationFile = $this->migrationsFolder . '/Migration'.$migrationId.'.php';

        if (!is_file($migrationFile)) {
            if (file_put_contents($migrationFile, sprintf(
                    self::CREATE_MIGRATION_FILE_PHP,
                    ltrim($this->migrationsNamespace, '\\'),
                    $migrationId
                )
            )) {
                return $migrationFile;
            }
        }

        return false;
    }
}
