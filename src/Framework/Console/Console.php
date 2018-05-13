<?php

namespace Framework\Console;

use Framework\Application;
use Symfony\Component\Console\Application as BaseConsole;

class Console extends BaseConsole
{
    private $frameworkApplication;
    private $projectDirectory;

    public function __construct(
        string $name = 'UNKNOWN',
        string $version = 'UNKNOWN',
        Application $frameworkApplication,
        string $projectDirectory = null
    ) {
        parent::__construct($name, $version);

        $this->frameworkApplication = $frameworkApplication;
        $this->projectDirectory = $projectDirectory;
    }

    /**
     * @return Application
     */
    public function getFrameworkApplication(): Application
    {
        return $this->frameworkApplication;
    }

    /**
     * @return string
     */
    public function getProjectDirectory(): string
    {
        return $this->projectDirectory;
    }
}
