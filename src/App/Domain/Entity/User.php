<?php declare(strict_types=1);

namespace App\Domain\Entity;

class User
{
    private $id;
    private $username;

    public function __construct(string $username)
    {
        $this->username = $username;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }
}
