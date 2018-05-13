<?php declare(strict_types=1);

namespace App\Domain\UseCase;

use App\Domain\Repository\UserRepositoryInterface;

class FindUserByUsername
{
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function run(string $username)
    {
        return $this->userRepository->getByUsername($username);
    }
}
