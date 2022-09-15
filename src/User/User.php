<?php
namespace Project\User;

use Project\Traits\Id;
class User implements UserInterface
{
    use Id;
    public function __construct(
        private string $firstName,
        private string $lastName,
        private string $email
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

}
