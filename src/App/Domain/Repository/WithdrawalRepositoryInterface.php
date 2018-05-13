<?php declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Account;

interface WithdrawalRepositoryInterface
{
    public function findAllByAccount(Account $account): array;
}
