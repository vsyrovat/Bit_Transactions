<?php declare(strict_types=1);

namespace App\Domain\Entity;

use Framework\Security\UserInterface;

class User implements UserInterface
{
    private $id;
    private $username;
    private $password;
    private $account;

    public function __construct(string $username, ?string $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    public function hasRole($role): bool
    {
        return in_array($role, $this->getRoles(), true);
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function eraseCredentials() { }
}
