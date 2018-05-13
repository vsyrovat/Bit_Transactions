<?php declare(strict_types=1);

$app['app.uc.findAccountByUsername'] = function(\Framework\Application $app) {
    return new \App\Domain\UseCase\FindAccountByUsername(
        $app['app.dao.userRepository'],
        $app['app.dao.accountRepository']
    );
};

$app['app.uc.createUser'] = function(\Framework\Application $app) {
    return new \App\Domain\UseCase\CreateUser(
        $app['app.dao.pdo'],
        $app['app.dao.userRepository'],
        $app['app.dao.accountRepository']
    );
};

$app['app.uc.createAccount'] = function(\Framework\Application $app) {
    return new \App\Domain\UseCase\CreateAccount(
        $app['app.dao.pdo'],
        $app['app.dao.accountRepository']
    );
};
