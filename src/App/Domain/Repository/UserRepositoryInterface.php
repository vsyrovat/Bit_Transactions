<?php declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\User;
use App\Domain\Exception\UnableCreateUserException;

interface UserRepositoryInterface
{
    /**
     * @param User $user
     * @throws UnableCreateUserException
     */
    public function create(User &$user): void;

    public function getByUsername(string $username): User;
}
