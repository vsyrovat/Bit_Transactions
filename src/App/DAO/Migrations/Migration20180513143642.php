<?php
namespace App\DAO\Migrations;

use \Framework\Migrations\AbstractMigration;

class Migration20180513143642 extends AbstractMigration
{
    public function run()
    {
        return $this->pdo->query(<<<'SQL'
CREATE TABLE `accounts` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `balance_amount` DECIMAL(10,2) NULL,
  `balance_currency` CHAR(3) NULL,
  PRIMARY KEY(`id`),
  INDEX `IDX_user_id`(`user_id`)
)
COLLATE='utf8_general_ci'
SQL
        );
    }
}
