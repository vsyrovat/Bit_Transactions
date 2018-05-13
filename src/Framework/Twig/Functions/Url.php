<?php

declare(strict_types=1);

namespace Framework\Twig\Functions;

use Symfony\Component\Routing\Generator\UrlGenerator;

class Url extends \Twig_Function
{
    public function __construct(UrlGenerator $urlGenerator)
    {
        parent::__construct(
            'url',
            function($name, $parameters = []) use ($urlGenerator) {
                return $urlGenerator->generate($name, $parameters);
            }
        );
    }
}
