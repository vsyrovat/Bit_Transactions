<?php declare(strict_types=1);

namespace App\DAO\Repository;

use App\Domain\Entity\User;
use App\Domain\Exception\UnableCreateUserException;
use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\Exception\UserNotFoundException;

class UserRepository implements UserRepositoryInterface
{
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(User &$user): void
    {
        if ($user->getId()) {
            throw new UnableCreateUserException();
        }

        $stmt = $this->pdo->prepare("INSERT INTO `users` SET `username`=:username");
        $stmt->execute(['username' => $user->getUsername()]);

        $insertId = $this->pdo->lastInsertId();

        $rpId = new \ReflectionProperty(get_class($user), 'id');
        $rpId->setAccessible(true);
        $rpId->setValue($user, intval($insertId));
    }

    public function getByUsername(string $username): User
    {
        $stmt = $this->pdo->prepare("SELECT * FROM `users` WHERE `username`=:username");
        $stmt->execute(['username' => $username]);

        if ($stmt->rowCount() == 0) {
            throw new UserNotFoundException;
        }

        return $this->buildObj($stmt->fetch(\PDO::FETCH_ASSOC));
    }

    private function buildObj(array $data)
    {
        $user = new User($data['username']);

        $rp = new \ReflectionProperty(get_class($user), 'id');
        $rp->setAccessible(true);
        $rp->setValue($user, intval($data['id']));

        return $user;
    }
}
