<?php declare(strict_types=1);

namespace Framework\Security;

use Symfony\Component\Security\Core\User\UserInterface as BaseUserInterface;

interface UserInterface extends BaseUserInterface
{
    public function hasRole($role): bool;
}
