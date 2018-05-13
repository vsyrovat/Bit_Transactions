<?php

declare(strict_types=1);

namespace Framework\Security;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class SecurityServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['auth'] = function ($app) {
            return new Auth($app['session'], $app['auth.users']);
        };

        $app['user'] = $app->factory(function ($app) {
            return $app['auth']->getUser();
        });
    }
}
