<?php declare(strict_types=1);

namespace App\Domain\UseCase;

use App\Domain\Entity\Account;
use App\Domain\Entity\Money;
use App\Domain\Entity\User;
use App\Domain\Repository\AccountRepositoryInterface;
use App\Domain\Repository\UserRepositoryInterface;

class CreateUser
{
    private $pdo;
    private $userRepository;
    private $accountRepositoy;

    public function __construct(\PDO $pdo, UserRepositoryInterface $userRepository, AccountRepositoryInterface $accountRepository)
    {
        $this->pdo = $pdo;
        $this->userRepository = $userRepository;
        $this->accountRepositoy = $accountRepository;
    }

    public function run(string $username, Money $balance = null): User
    {
        try {
            $this->pdo->beginTransaction();

            $user = new User($username);
            $this->userRepository->create($user);

            $account = new Account($user, $balance ?: new Money(null));
            $this->accountRepositoy->create($account);

            $this->pdo->commit();

            return $user;
        } catch (\Exception $e) {
            $this->pdo->rollBack();

            throw $e;
        }
    }
}
