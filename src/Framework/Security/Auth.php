<?php

declare(strict_types=1);

namespace Framework\Security;

use Symfony\Component\HttpFoundation\Session\Session;

class Auth
{
    private $session;
    private $users;

    public function __construct(Session $session, array $users)
    {
        $this->session = $session;

        foreach ($users as $login => $userData) {
            if (!is_array($userData[0])) {
                throw new \InvalidArgumentException('Grants should be array (for user '.$login.'), '.$userData[0].' given');
            }
            if (!is_scalar($userData[1]) && !empty($userData[1])) {
                throw new \InvalidArgumentException('Password hash should be string (for user '.$login.') '.$userData[1].' given');
            }
        }

        $this->users = $users;
    }

    public function tryLogin(string $login, string $password): bool
    {
        if (isset($this->users[$login])) {
            $grants = $this->users[$login][0];
            $hash = strval($this->users[$login][1]);

            if (password_verify($password, $hash)) {
                $this->session->set('user', new User($login, $grants));
                return true;
            }
        }

        return false;
    }

    public function getUser(): User
    {
        if (!$this->session->has('user')) {
            $user = new User();
            $this->session->set('user', $user);
        } else {
            $user = $this->session->get('user');
            $actualGrants = $this->getActualGrantsByUsername($user->getUsername());
            if ($user->getGrants() != $actualGrants) {
                $user->setGrants($actualGrants);
            }
        }

        return $user;
    }

    public function isGrant(string $grant): bool
    {
        return $this->getUser()->isGrant($grant);
    }

    protected function getActualGrantsByUsername(?string $username): array
    {
        if ($username === null) {
            return [];
        }

        if (isset($this->users[$username])) {
            return $this->users[$username][0];
        }

        return [];
    }
}
