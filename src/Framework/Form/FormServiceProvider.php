<?php

declare(strict_types=1);

namespace Framework\Form;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\Extension\Csrf\CsrfExtension;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\Forms;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;
use Symfony\Component\Security\Csrf\TokenStorage\SessionTokenStorage;
use Symfony\Component\Translation\Translator;

class FormServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        if (class_exists('\Symfony\Bridge\Twig\AppVariable')) {
            $appVariableReflection = new \ReflectionClass('\Symfony\Bridge\Twig\AppVariable');
            $vendorTwigBridgeDirectory = dirname($appVariableReflection->getFileName());
            $app['twig.loader.filesystem']->addPath($vendorTwigBridgeDirectory.'/Resources/views/Form');
        }

        $app['twig']->addExtension(new FormExtension());

        $app['twig']->addExtension(new TranslationExtension($app['translator']));

        $app['twig']->addRuntimeLoader(new \Twig_FactoryRuntimeLoader([
            FormRenderer::class => function() use ($app) {
                return new FormRenderer($app['form.engine'], $app['csrf.token_manager']);
            }
        ]));

        $app['form.engine'] = function ($app) {
            return new TwigRendererEngine(['bootstrap_4_layout.html.twig'], $app['twig']);
        };

        $app['csrf.token_generator'] = function ($app) {
            return new UriSafeTokenGenerator();
        };

        $app['csrf.token_storage'] = function ($app) {
            return new SessionTokenStorage($app['session']);
        };

        $app['csrf.token_manager'] = function ($app) {
            return new CsrfTokenManager($app['csrf.token_generator'], $app['csrf.token_storage']);
        };

        $app['form.factory'] = function ($app) {
            return Forms::createFormFactoryBuilder()
                ->addExtension(new HttpFoundationExtension())
                ->addExtension(new CsrfExtension($app['csrf.token_manager']))
                ->getFormFactory();
        };
    }
}
