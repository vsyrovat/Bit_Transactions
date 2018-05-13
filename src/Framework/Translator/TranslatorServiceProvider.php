<?php

declare(strict_types=1);

namespace Framework\Translator;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\Translation\Translator;

class TranslatorServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['translator'] = function ($app) {
            if (!isset($app['locale'])) {
                throw new \LogicException('You must define \'locale\' parameter');
            }

            $translator = new Translator($app['locale']);

            return $translator;
        };
    }
}
