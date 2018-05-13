<?php
namespace App\DAO\Migrations;

use \Framework\Migrations\AbstractMigration;

class Migration20180513143400 extends AbstractMigration
{
    public function run()
    {
        return $this->pdo->query(<<<'SQL'
CREATE TABLE `users` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255),
    PRIMARY KEY (`id`),
    UNIQUE INDEX `IDX_username` (`username`)
)
COLLATE='utf8_general_ci'
SQL
        );
    }
}
