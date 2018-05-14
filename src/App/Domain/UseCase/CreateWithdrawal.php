<?php declare(strict_types=1);

namespace App\Domain\UseCase;

use App\Domain\Entity\Account;
use App\Domain\Entity\Money;
use App\Domain\Entity\User;
use App\Domain\Entity\Withdrawal;
use App\Domain\Exception\UnableCreateWithdrawalException;
use App\Domain\Repository\AccountRepositoryInterface;
use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\Repository\WithdrawalRepositoryInterface;

class CreateWithdrawal
{
    private $pdo;
    private $userRepository;
    private $accountRepository;
    private $withdrawalRepository;

    public function __construct(
        \PDO $pdo,
        UserRepositoryInterface $userRepository,
        AccountRepositoryInterface $accountRepository,
        WithdrawalRepositoryInterface $withdrawalRepository
    ) {
        $this->pdo = $pdo;
        $this->userRepository = $userRepository;
        $this->accountRepository = $accountRepository;
        $this->withdrawalRepository = $withdrawalRepository;
    }

    public function run(User $user, Account $account, Money $value)
    {
        if ($value->getAmountCent() <= 0) {
            return new \InvalidArgumentException("Withdrawal amount should be positive");
        }

        try {
            $this->pdo->exec('SET TRANSACTION ISOLATION LEVEL SERIALIZABLE');
            $this->pdo->beginTransaction();

            $actualUser = $this->userRepository->findById($user->getId());
            $actualAccount = $this->accountRepository->findById($account->getId());

            if ($actualAccount->getUser()->getId() === $actualUser->getId()) {
                if ($actualAccount->getBalance()->isGreaterOrEqualThan($value)) {
                    $withdrawal = new Withdrawal($actualAccount, $value, Withdrawal::STATUS_PENDING);
                    $this->withdrawalRepository->create($withdrawal);

                    $this->accountRepository->updateBalance($actualAccount, $actualAccount->getBalance()->sub($value));
                } else {
                    throw new \InvalidArgumentException('Requested value is bigger than balance');
                }
            } else {
                throw new \RuntimeException('Fail according account to user object');
            }

            $this->pdo->commit();
        } catch (\Exception $e) {
            $this->pdo->rollBack();

            throw new UnableCreateWithdrawalException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
