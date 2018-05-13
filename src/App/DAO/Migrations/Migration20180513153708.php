<?php
namespace App\DAO\Migrations;

use \Framework\Migrations\AbstractMigration;

class Migration20180513153708 extends AbstractMigration
{
    public function run()
    {
        return $this->pdo->query(<<<'SQL'
CREATE TABLE `withdrawals` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `account_id` INT NOT NULL,
  `money_amount` DECIMAL(10,2) NULL,
  `money_currency` CHAR(3) NULL,
  `status` INT,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `IDX_account_id` (`account_id`)
)
COLLATE='utf8_general_ci'
SQL
        );
    }
}
