<?php

namespace Framework\Console;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ConsoleServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['console'] = function() use ($pimple) {
            return new Console(
                $pimple['console.name'],
                $pimple['console.version'],
                $pimple,
                $pimple['console.project_directory']
            );
        };
    }
}
