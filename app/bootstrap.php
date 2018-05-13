<?php declare(strict_types=1);

require_once 'autoload.php';
require_once 'config.php';

$app = new \Framework\Application;

$app['debug'] = defined('DEBUG') && DEBUG;

$app['locale'] = 'en';

$app->register(new \Framework\Twig\TwigServiceProvider(), [
    'twig.path' => APP_ROOT . '/src/App/UI/View',
    'twig.options' => [
        'cache' => TWIG_CACHE_DIR,
    ],
]);

$app->register(new \Framework\Translator\TranslatorServiceProvider());

$app->register(new \Framework\Form\FormServiceProvider());

//$app->register(new \Framework\Pagination\PaginationServiceProvider());
//
$app->register(new \Framework\Security\SecurityServiceProvider(), [
    'auth.users' => [
        'user' => [['ROLE_USER'], '$2y$10$/FEy0qFDzY3y3q9gLdjlYu0HP9IKVvk57Wsdb/XeiMY4dCWiwTYga'], // 123456
    ],
]);

require_once 'routes.php';
//require_once 'use_cases.php';
//require_once 'dao.php';
