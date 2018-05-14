<?php declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Account;
use App\Domain\Entity\Withdrawal;

interface WithdrawalRepositoryInterface
{
    public function create(Withdrawal &$withdrawal): void;

    public function findAllByAccount(Account $account): array;
}
