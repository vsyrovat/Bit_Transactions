<?php declare(strict_types=1);

namespace App\Domain\Entity;

class Account
{
    private $id;
    private $login;
    private $passhash;

    public function __construct(string $login, string $passhash)
    {
        $this->login = $login;
        $this->passhash = $passhash;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getPasshash(): string
    {
        return $this->passhash;
    }
}
