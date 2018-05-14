<?php declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Account;
use App\Domain\Entity\Money;
use App\Domain\Entity\User;
use App\Domain\Exception\AccountNotFoundException;

interface AccountRepositoryInterface
{
    public function create(Account &$account): void;

    /**
     * @throws AccountNotFoundException
     */
    public function findById(int $id): Account;

    public function findFirstByUser(User $user): Account;

    public function updateBalance(Account $account, Money $balance): void;
}
