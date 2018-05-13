<?php

namespace Framework\Migrations\Drivers;

use Framework\Migrations\DriverInterface;

class Mysql implements DriverInterface
{
    public function getSchemaMigrationsTableSql(): string
    {
        return "SHOW TABLES LIKE 'schema_migrations'";
    }

    public function getCreateMigrationsTableSql(): string
    {
        return <<<'SQL'
CREATE TABLE `schema_migrations` (
  `id` CHAR(20) NOT NULL,
  PRIMARY KEY (`id`)
)    
SQL;
    }

    public function getInsertMigrationIdSql(): string
    {
        return "INSERT INTO `schema_migrations` (`id`) VALUES (:id)";
    }

    public function getFetchMigrationIdsSql(): string
    {
        return "SELECT `id` FROM `schema_migrations` ORDER BY `id`";
    }
}
