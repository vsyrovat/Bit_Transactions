<?php declare(strict_types=1);

namespace App\DAO\Repository;

use App\Domain\Entity\Account;
use App\Domain\Entity\Money;
use App\Domain\Entity\Withdrawal;
use App\Domain\Repository\WithdrawalRepositoryInterface;

class WithdrawalRepository implements WithdrawalRepositoryInterface
{
    private $table = 'withdrawals';
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
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
