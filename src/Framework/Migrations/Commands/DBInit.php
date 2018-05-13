<?php
namespace Framework\Migrations\Commands;

use Framework\Console\Command;
use Framework\Migrations\Migrator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DBInit extends Command
{
    protected $migrator;

    public function __construct($name, Migrator $migrator)
    {
        $this->migrator = $migrator;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setName('db:init')
            ->setDescription('Init application database(s)');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->migrator->setOutput($output);

        $this->migrator->initSchema();
    }
}
