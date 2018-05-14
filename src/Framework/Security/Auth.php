<?php

declare(strict_types=1);

namespace Framework\Security;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class Auth
{
    private $session;
    private $userProvider;

    public function __construct(Session $session, UserProviderInterface $userProvider)
    {
        $this->session = $session;
        $this->userProvider = $userProvider;
    }

    public function tryLogin(string $login, string $password): bool
    {
        try {
            $user = $this->userProvider->loadUserByUsername($login);

            if (password_verify($password, strval($user->getPassword()))) {
                $this->session->set('user', $user);
                return true;
            }
        } catch (UsernameNotFoundException $e) {
            // Protection against user bruteforce-search
            // password_verify is time-attack-safe, so we just call it if user not found
            // Next line make tryLogin execution time equal in both cases: when user exists and when don't
            // '$2y$10$/FEy0qFDzY3y3q9gLdjlYu0HP9IKVvk57Wsdb/XeiMY4dCWiwTYga' == password_hash('123456', PASSWORD_BCRYPT)
            password_verify($password, '$2y$10$/FEy0qFDzY3y3q9gLdjlYu0HP9IKVvk57Wsdb/XeiMY4dCWiwTYga');
        }

        return false;
    }

    public function getUser(): UserInterface
    {
        return $this->session->get('user') ?: new NullUser();
    }

    public function isGrant(string $grant): bool
    {
        return $this->session->get('user');
    }
}
