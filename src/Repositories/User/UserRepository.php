<?php
namespace Project\Repositories\User;

use Project\Connection\ConnectorInterface;
use Project\Connection\DataBaseConnector;
use Project\Exceptions\UserNotFoundException;
use Project\User\User;
use PDO;


class UserRepository implements UserRepositoryInterface
{
    private PDO $connection;

    public function __construct(private ?ConnectorInterface $connector = null)
    {
        $this->connector = $connector ?? new DataBaseConnector();
        $this->connection = $this->connector->getConnection();
    }
    public function save(User $user): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO user (first_name, last_name, email)
            VALUES (:first_name, :last_name, :email)'
        );
        $statement->execute([
            ':first_name' => $user->getFirstName(),
            ':last_name' => $user->getLastName(),
            ':email' => $user->getEmail(),
        ]);
    }

    /**
     * @throws UserNotFoundException
     * @throws \Exception
     */
    public function get(int $id): User
    {
        $statement = $this->connection->prepare(
            "select * from user where id = :userId"
        );

        $statement->execute([
            'userId' => $id
        ]);

        $userObj = $statement->fetch(PDO::FETCH_OBJ);

        if(!$userObj)
        {
            throw new UserNotFoundException("User with id: $id not found");
        }

        return $this->mapUser($userObj);
    }

    public function findUserByEmail(string $email): User
    {
        $statement = $this->connection->prepare(
            "select * from user where email = :email"
        );

        $statement->execute([
            'email' => $email
        ]);

        $userObj = $statement->fetch(PDO::FETCH_OBJ);

        if(!$userObj)
        {
            throw new UserNotFoundException("User with email: $email not found");
        }

        return $this->mapUser($userObj);

    }

    public function mapUser(object $userObj): User
    {

        $user = new User($userObj->first_name, $userObj->last_name, $userObj->email);

        $user->setId($userObj->id);

        return $user;
    }

    
}