<?php
namespace Project\Repositories\AuthToken;

use Project\AuthToken\AuthToken;
use Project\Connection\ConnectorInterface;
use Project\Connection\DataBaseConnector;
use PDO;
use DateTimeInterface;
use PDOException;
use DateTimeImmutable;
use Exception;
use Project\Exceptions\AuthTokenRepositoryException;
use Project\Exceptions\AuthTokenNotFoundException;

class AuthTokenRepository implements AuthTokenRepositoryInterface
{
    private PDO $connection;    
    public function __construct(
        private ?ConnectorInterface $connector = null
    )
    {
        $this->connector = $connector ?? new DataBaseConnector();
        $this->connection = $this->connector->getConnection();
    }
    public function save(AuthToken $authToken): void
    {
        $query = <<<'SQL'
            INSERT INTO tokens (
                token,
                email,
                expires_on
            ) VALUES (
                :token,
                :email,
                :expires_on
            )
            ON CONFLICT (token) DO UPDATE SET
                expires_on = :expires_on
        SQL;

        try {
            $statement = $this->connection->prepare($query);
            $statement->execute([
                ':token' => (string)$authToken->token(),
                ':email' => (string)$authToken->email(),
                ':expires_on' => $authToken->expiresOn()
                    ->format(DateTimeInterface::ATOM),
            ]);
        } catch (PDOException $e) {
            throw new AuthTokenRepositoryException(
                $e->getMessage(), (int)$e->getCode(), $e
            );
        }
    }
    public function get(string $token): AuthToken
    {
        try {
        $statement = $this->connection->prepare(
            'SELECT * FROM tokens WHERE token = ?'
        );

        $statement->execute([$token]);
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new AuthTokenRepositoryException(
                $e->getMessage(), (int)$e->getCode(), $e
            );
        }

        if (false === $result) 
        {
            throw new AuthTokenNotFoundException("Cannot find token: $token");
        }
        try {
            return new AuthToken(
                $result['token'],
                $result['email'],
                new DateTimeImmutable($result['expires_on'])
        );
        } catch (Exception $e) {
            throw new AuthTokenRepositoryException(
                $e->getMessage(), $e->getCode(), $e
            );
        }
    }
}
