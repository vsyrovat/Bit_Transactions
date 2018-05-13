<?php declare(strict_types=1);

define('DEBUG', true);

require_once __DIR__ . '/../app/bootstrap.php';

$app->run();

if ($app['debug']) {
    $app->register(new \Framework\Debug\PimpleDumpProvider(APP_ROOT));
}
