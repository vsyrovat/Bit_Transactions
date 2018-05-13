<?php declare(strict_types=1);

namespace App\Domain\UseCase;

use App\Domain\Entity\Account;
use App\Domain\Entity\Money;
use App\Domain\Entity\User;
use App\Domain\Repository\AccountRepositoryInterface;

class CreateAccount
{
    private $pdo;
    private $accountRepository;

    public function __construct(\PDO $pdo, AccountRepositoryInterface $accountRepository)
    {
        $this->pdo = $pdo;
        $this->accountRepository = $accountRepository;
    }

    public function run(User $user, Money $balance = null): Account
    {
        try {
            $this->pdo->beginTransaction();

            $account = new Account($user, $balance ?: new Money(0, 'USD'));

            $this->accountRepository->create($account);

            $this->pdo->commit();

            return $account;
        } catch (\Exception $e) {
            $this->pdo->rollback();

            throw $e;
        }
    }
}
