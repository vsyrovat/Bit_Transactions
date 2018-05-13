<?php declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

$loader = new \Composer\Autoload\ClassLoader();

$loader->add('Framework', __DIR__.'/../src');
$loader->add('App', __DIR__.'/../src');

$loader->register();
