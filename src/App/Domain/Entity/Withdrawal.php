<?php declare(strict_types=1);

namespace App\Domain\Entity;

class Withdrawal
{
    const STATUS_UNKNOWN = 0;
    const STATUS_PENDING = 1;
    const STATUS_SUCCESS = 2;
    const STATUS_FAILURE = 3;

    private $id;
    private $account;
    private $money;
    private $createdAt;
    private $status;

    public function __construct(Account $account, Money $money, ?int $status)
    {
        $this->account = $account;
        $this->money = $money;
        $this->createdAt = new \DateTimeImmutable;
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Account
     */
    public function getAccount(): Account
    {
        return $this->account;
    }

    /**
     * @return Money
     */
    public function getMoney(): Money
    {
        return $this->money;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getStatus(): int
    {
        return $this->status;
    }
}
