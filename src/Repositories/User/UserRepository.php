<?php
namespace Project\Repositories\User;

use Project\Connection\ConnectorInterface;
use Project\Connection\DataBaseConnector;
use Project\Blog\User\User;
use PDO;
use Psr\Log\LoggerInterface;


class UserRepository implements UserRepositoryInterface
{
    private PDO $connection;

    public function __construct(
        private LoggerInterface $logger,
        private ?ConnectorInterface $connector = null
    )
    {
        $this->connector = $connector ?? new DataBaseConnector();
        $this->connection = $this->connector->getConnection();
    }
    public function save(User $user): void
    {
        $firstName = $user->getFirstName();
        $lastName = $user->getLastName();
        $email = $user->getEmail();

        $statement = $this->connection->prepare(
            'INSERT INTO user (first_name, last_name, email)
            VALUES (:first_name, :last_name, :email)'
        );
        $statement->execute([
            ':first_name' => $firstName,
            ':last_name' => $lastName,
            ':email' => $email,
        ]);
        
        $this->logger->info("User $firstName $lastName, $email created");
    }

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
            $this->logger->warning("User with id: $id not found");
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
            $this->logger->warning("User with email: $email not found");
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