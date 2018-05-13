<?php

declare(strict_types=1);

namespace Framework\Twig;

use Framework\Twig\Functions\FileWithMtime;
use Framework\Twig\Functions\Url;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class TwigServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['twig.options'] = [];

        $app['twig'] = function ($app) {
            $app['twig.options'] = array_replace([
                'charset' => $app['charset'],
                'debug' => $app['debug'],
                'strict_variables' => $app['debug'],
            ], $app['twig.options']);

            /* @var $twig \Twig_Environment */
            $twig = $app['twig.environment_factory']($app);

            if ($app['debug']) {
                $twig->addExtension(new \Twig_Extension_Debug());
            }

            $twig->addFunction(new Url($app['url_generator']));

            $twig->addFunction(new FileWithMtime());

            $twig->addGlobal('app', $app);

            return $twig;
        };

        $app['twig.environment_factory'] = $app->protect(function ($app) {
            return new \Twig_Environment($app['twig.loader.filesystem'], $app['twig.options']);
        });

        $app['twig.loader.filesystem'] = function ($app) {
            return new \Twig_Loader_Filesystem($app['twig.path']);
        };
    }
}
