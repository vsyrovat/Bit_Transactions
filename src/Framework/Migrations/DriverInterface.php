<?php

namespace Framework\Migrations;

interface DriverInterface
{
    public function getSchemaMigrationsTableSql(): string;

    public function getCreateMigrationsTableSql(): string;

    public function getInsertMigrationIdSql(): string;

    public function getFetchMigrationIdsSql(): string;
}
