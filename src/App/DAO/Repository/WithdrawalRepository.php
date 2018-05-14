<?php declare(strict_types=1);

namespace App\DAO\Repository;

use App\Domain\Entity\Account;
use App\Domain\Entity\Money;
use App\Domain\Entity\Withdrawal;
use App\Domain\Exception\UnableCreateWithdrawalException;
use App\Domain\Repository\WithdrawalRepositoryInterface;

class WithdrawalRepository implements WithdrawalRepositoryInterface
{
    private $table = 'withdrawals';
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(Withdrawal &$withdrawal): void
    {
        if ($withdrawal->getId()) {
            throw new UnableCreateWithdrawalException('Withdrawal already persist. You should create new instance of Withdrawal object.');
        }

        $stmt = $this->pdo->prepare("INSERT INTO `{$this->table}` 
SET
  `account_id`=:accountId,
  `money_amount`=:moneyAmount,
  `money_currency`=:moneyCurrency,
  `status`=:status,
  `created_at`=:createdAt");
        $stmt->execute([
            'accountId' => $withdrawal->getAccount()->getId(),
            'moneyAmount' => $withdrawal->getMoney()->getAmount(),
            'moneyCurrency' => $withdrawal->getMoney()->getCurrency(),
            'status' => $withdrawal->getStatus(),
            'createdAt' => $withdrawal->getCreatedAt()->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i:s'),
        ]);

        $insertId = $this->pdo->lastInsertId();

        $rpId = new \ReflectionProperty(get_class($withdrawal), 'id');
        $rpId->setAccessible(true);
        $rpId->setValue($withdrawal, intval($insertId));
    }

    public function findAllByAccount(Account $account): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM `{$this->table}` WHERE `account_id`=:accountId");
        $stmt->execute(['accountId' => $account->getId()]);

        $withdrawals = [];

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $withdrawals[] = $this->buildObj($row, $account);
        }

        return $withdrawals;
    }

    private function buildObj(array $data, Account $account): Withdrawal
    {
        $obj = new Withdrawal($account, new Money($data['money_amount'], $data['money_currency']), intval($data['status']));

        $rpId = new \ReflectionProperty(get_class($obj), 'id');
        $rpId->setAccessible(true);
        $rpId->setValue($obj, intval($data['id']));

        $rpCreatedAt = new \ReflectionProperty(get_class($obj), 'createdAt');
        $rpCreatedAt->setAccessible(true);
        $rpCreatedAt->setValue($obj, new \DateTimeImmutable($data['created_at'], new \DateTimeZone('UTC')));

        return $obj;
    }
}
