<?php declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Account;
use App\Domain\Entity\User;
use App\Domain\Exception\AccountNotFoundException;

interface AccountRepositoryInterface
{
    public function create(Account &$account): void;

    /**
     * @param string $username
     * @return Account
     * @throws AccountNotFoundException
     */
    public function findById(string $username): Account;

    public function findFirstByUser(User $user): Account;
}
