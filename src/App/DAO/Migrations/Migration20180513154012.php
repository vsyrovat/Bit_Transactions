<?php
namespace App\DAO\Migrations;

use \Framework\Migrations\AbstractMigration;

class Migration20180513154012 extends AbstractMigration
{
    public function run()
    {
        return $this->pdo->query(<<<'SQL'
ALTER TABLE `withdrawals` ADD CONSTRAINT `fk_withdrawals_accounts` FOREIGN KEY (`account_id`) REFERENCES `accounts`(`id`)
SQL
        );
    }
}
