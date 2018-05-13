<?php

namespace Framework\Console;

use Symfony\Component\Console\Command\Command as BaseCommand;

/**
 * @method \Framework\Console\Console getApplication();
 */
class Command extends BaseCommand
{
    public function getFrameworkApplication()
    {
        return $this->getApplication()->getFrameworkApplication();
    }

    public function getProjectDirectory()
    {
        return $this->getApplication()->getProjectDirectory();
    }
}
