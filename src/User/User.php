<?php
namespace ChalkinaNatalia\Project\User;
class User
{
    public function __construct(
        private int $id,
        private string $firstName,
        private string $lastName
    ) {
    }
    public function __toString()
    {
        return $this->firstName . ' ' . $this->lastName;
    }
}
