<?php declare(strict_types=1);

$app->map('/', \App\UI\Controller\DefaultController::class.'::indexAction', '/');

$app->map('/login', \App\UI\Controller\DefaultController::class.'::loginAction', 'login');
$app->map('/logout', \App\UI\Controller\DefaultController::class.'::logoutAction', 'logout');
