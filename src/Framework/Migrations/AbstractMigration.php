<?php
namespace Framework\Migrations;

abstract class AbstractMigration
{
    protected $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    abstract public function run();
}
