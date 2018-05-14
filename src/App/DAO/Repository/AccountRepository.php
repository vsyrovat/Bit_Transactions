<?php declare(strict_types=1);

namespace App\DAO\Repository;

use App\Domain\Entity\Account;
use App\Domain\Exception\UnableCreateAccountException;
use App\Domain\Repository\AccountRepositoryInterface;
use App\Domain\Entity\Money;
use App\Domain\Entity\User;
use App\Domain\Exception\AccountNotFoundException;
use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\Repository\WithdrawalRepositoryInterface;

class AccountRepository implements AccountRepositoryInterface
{
    private $pdo;
    private $table = 'accounts';
    private $userRepository;
    private $withdrawalRepository;

    public function __construct(\PDO $pdo, UserRepositoryInterface $userRepository, WithdrawalRepositoryInterface $withdrawalRepository)
    {
        $this->pdo = $pdo;
        $this->userRepository = $userRepository;
        $this->withdrawalRepository = $withdrawalRepository;
    }

    public function create(Account &$account): void
    {
        if ($account->getId()) {
            throw new UnableCreateAccountException();
        }

        $stmt = $this->pdo->prepare("INSERT INTO `accounts` SET `user_id`=:userId, `balance_amount`=:balanceAmount, `balance_currency`=:balanceCurrency");
        $stmt->execute([
            'userId' => $account->getUser()->getId(),
            'balanceAmount' => $account->getBalance()->getAmount(),
            'balanceCurrency' => $account->getBalance()->getCurrency(),
        ]);

        $insertId = $this->pdo->lastInsertId();

        $rpId = new \ReflectionProperty(get_class($account), 'id');
        $rpId->setAccessible(true);
        $rpId->setValue($account, intval($insertId));
    }

    public function findById(int $id): Account
    {
        $stmt = $this->pdo->prepare("SELECT * FROM `{$this->table}` WHERE id=:id");
        $stmt->execute(['id' => $id]);

        if ($stmt->rowCount() == 0) {
            throw new AccountNotFoundException();
        }

        return $this->buildObject($stmt->fetch(\PDO::FETCH_ASSOC));
    }

    public function findFirstByUser(User $user): Account
    {
        $stmt = $this->pdo->prepare("SELECT * FROM `{$this->table}` WHERE `user_id`=:userId ORDER BY `id` LIMIT 1");
        $stmt->execute(['userId' => $user->getId()]);

        if ($stmt->rowCount() == 0) {
            throw new AccountNotFoundException();
        }

        return $this->buildObject($stmt->fetch(\PDO::FETCH_ASSOC), $user);
    }

    public function updateBalance(Account $account, Money $balance): void
    {
        $stmt = $this->pdo->prepare("UPDATE `{$this->table}` SET `balance_amount`=:balanceAmount, `balance_currency`=:balanceCurrency WHERE `id`=:id");
        $stmt->execute([
            'balanceAmount' => $balance->getAmount(),
            'balanceCurrency' => $balance->getCurrency(),
            'id' => $account->getId(),
        ]);
    }

    private function buildObject(array $data, User $user = null)
    {
        if ($user === null) {
            $user = $this->userRepository->findById(intval($data['user_id']));
        }

        $account = new Account($user, new Money($data['balance_amount'], $data['balance_currency']));

        $rpId = new \ReflectionProperty(get_class($account), 'id');
        $rpId->setAccessible(true);
        $rpId->setValue($account, intval($data['id']));

        $rpWithdrawals = new \ReflectionProperty(get_class($account), 'withdrawals');
        $rpWithdrawals->setAccessible(true);
        $rpWithdrawals->setValue($account, $this->withdrawalRepository->findAllByAccount($account));

        return $account;
    }
}
