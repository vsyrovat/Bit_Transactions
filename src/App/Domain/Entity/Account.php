<?php declare(strict_types=1);

namespace App\Domain\Entity;

class Account
{
    private $id;
    private $user;
    private $balance;
    private $withdrawals;

    public function __construct(User $user, Money $balance)
    {
        $this->user = $user;
        $this->balance = $balance;
        $this->withdrawals = [];
    }

    /**
     * @return mixed
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return Money
     */
    public function getBalance(): Money
    {
        return $this->balance;
    }

    /**
     * @return Withdrawal[]
     */
    public function getWithdrawals(): array
    {
        return $this->withdrawals;
    }
}
