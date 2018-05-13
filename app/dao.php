<?php declare(strict_types=1);

$app['app.dao.pdo'] = function(\Framework\Application $app){
    $pdo = new \PDO(APP_DB_PDO_DSN, APP_DB_USER, APP_DB_PASSWORD);
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(\PDO::ATTR_STATEMENT_CLASS, [\Framework\PDO\PDOStatement::class, []]);
    return $pdo;
};

$app['app.dao.userRepository'] = function(\Framework\Application $app) {
    return new \App\DAO\Repository\UserRepository($app['app.dao.pdo']);
};

$app['app.dao.accountRepository'] = function(\Framework\Application $app) {
    return new \App\DAO\Repository\AccountRepository($app['app.dao.pdo'], $app['app.dao.withdrawalRepository']);
};

$app['app.dao.withdrawalRepository'] = function(\Framework\Application $app) {
    return new \App\DAO\Repository\WithdrawalRepository($app['app.dao.pdo']);
};
