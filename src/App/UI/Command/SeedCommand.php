<?php declare(strict_types=1);

namespace App\UI\Command;

use App\Domain\Entity\Money;
use Framework\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SeedCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('app:seed')
            ->setDescription('Seed the database with demo data');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = $this->getFrameworkApplication();

        $app['app.uc.createUser']->run('user1', new Money(100, 'USD'));
        $output->writeln('Created "user1" with 100 USD');

        $app['app.uc.createUser']->run('user2', new Money(200, 'USD'));
        $output->writeln('Created "user2" with 200 USD');
    }

}
