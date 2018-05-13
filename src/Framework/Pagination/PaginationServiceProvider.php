<?php

declare(strict_types=1);

namespace Framework\Pagination;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class PaginationServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['twig.loader.filesystem']->addPath(__DIR__.'/view', 'Pagination');
    }
}
