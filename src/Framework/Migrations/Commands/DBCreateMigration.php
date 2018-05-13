<?php
namespace Framework\Migrations\Commands;

use Framework\Console\Command;
use Framework\Migrations\Migrator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DBCreateMigration extends Command
{
    protected $migrator;

    public function __construct($name, Migrator $migrator)
    {
        $this->migrator = $migrator;
        parent::__construct($name);
    }

    public function configure()
    {
        $this
            ->setName('db:create-migration')
            ->setDescription('Create migration file');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->migrator->setOutput($output);

        $file = $this->migrator->createMigrationFile();

        if ($file) {
            $output->writeln("Created migration file: $file");
        } else {
            $output->writeln("Migration file was not created");
        }
    }
}
