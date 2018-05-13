<?php

declare(strict_types=1);

namespace Framework\Security;

class User
{
    private $username;
    private $grants = [];

    /**
     * BaseUser constructor.
     * @param string $username
     * @param string[] $grants
     */
    public function __construct(string $username = null, array $grants = [])
    {
        $this->username = $username;

        foreach ($grants as $grant) {
            $this->grants[] = strtoupper($grant);
        }
    }

    public function __isset($name)
    {
        return false;
    }

    /**
     * @return string
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @return array|string[]
     */
    public function getGrants(): array
    {
        return $this->grants;
    }

    /**
     * @param array $grants
     */
    public function setGrants(array $grants): void
    {
        $this->grants = $grants;
    }

    public function isGrant(string $grant): bool
    {
        return in_array(strtoupper($grant), $this->grants);
    }

    public function isLogged(): bool
    {
        return !is_null($this->username);
    }
}
