<?php
namespace App\DAO\Migrations;

use \Framework\Migrations\AbstractMigration;

class Migration20180513144041 extends AbstractMigration
{
    public function run()
    {
        return $this->pdo->query(<<<'SQL'
ALTER TABLE `accounts` ADD CONSTRAINT `fk_accounts_users` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
SQL
        );
    }
}
