<?php declare(strict_types=1);

namespace App\Domain\UseCase;

use App\Domain\Repository\AccountRepositoryInterface;
use App\Domain\Repository\WithdrawalRepositoryInterface;

class CreateWithdrawal
{
    private $pdo;
    private $accountRepository;
    private $withdrawalRepository;

    public function __construct(\PDO $pdo, AccountRepositoryInterface $accountRepository, WithdrawalRepositoryInterface $withdrawalRepository)
    {
        $this->pdo = $pdo;
        $this->accountRepository = $accountRepository;
        $this->withdrawalRepository = $withdrawalRepository;
    }

    public function run($account, $value)
    {
    }
}
