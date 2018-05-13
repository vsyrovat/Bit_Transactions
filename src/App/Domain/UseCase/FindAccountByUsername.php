<?php declare(strict_types=1);

namespace App\Domain\UseCase;

use App\Domain\Repository\AccountRepositoryInterface;
use App\Domain\Repository\UserRepositoryInterface;

class FindAccountByUsername
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;
    /**
     * @var AccountRepositoryInterface
     */
    private $accountRepository;

    public function __construct(UserRepositoryInterface $userRepository, AccountRepositoryInterface $accountRepository)
    {
        $this->userRepository = $userRepository;
        $this->accountRepository = $accountRepository;
    }

    public function run(string $username)
    {
        return
            $this->accountRepository->findFirstByUser(
                $this->userRepository->getByUsername($username)
            );
    }
}
