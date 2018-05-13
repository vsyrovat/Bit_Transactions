<?php declare(strict_types=1);

namespace Framework\Security;

class NullUser implements UserInterface
{
    public function hasRole($role): bool
    {
        return false;
    }

    public function getRoles()
    {
        return [];
    }

    public function getPassword()
    {
        return null;
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername()
    {
        return null;
    }

    public function eraseCredentials()
    {
    }
}
