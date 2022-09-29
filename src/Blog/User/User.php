<?php
namespace Project\Blog\User;

use Project\Traits\Id;
class User implements UserInterface
{
    use Id;
    public function __construct(
        private string $firstName,
        private string $lastName,
        private string $email, 
        private string $hashedPassword,
    ) {
    }
    public function __toString()
    {
        return $this->firstName . ' ' . $this->lastName . ' ' . $this->email;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getHashedPassword(): string
    {
        return $this->hashedPassword;
    }

    private static function hash(string $password, $email): string
    {
    return hash('sha256', $email . $password);
    }

    public function checkPassword(string $password): bool
    {
        return $this->hashedPassword
        === self::hash($password, $this->email);
    }

    public static function createFrom(
        string $firstName,
        string $lastName,
        string $email,
        string $password,
        ): self
    {
    return new self(
        $firstName,
        $lastName,
        $email,
        self::hash($password, $email),
        );
    }

}
