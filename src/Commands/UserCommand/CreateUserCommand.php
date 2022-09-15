<?php

namespace Project\Commands\UserCommand;

use Project\Exceptions\CommandException;
use Project\Exceptions\UserNotFoundException;
use Project\Repositories\User\UserRepositoryInterface;
use Project\User\User;
use Project\Argument\Argument;

class CreateUserCommand implements CreateUserCommandInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        )
    {
    }

    public function handle(Argument $argument):void
    {
        $firstName = $argument->get('firstName');
        $lastName = $argument->get('lastName');
        $email = $argument->get('email');

        if($this->userExists($email))
        {
            throw new CommandException("User exists: $email".PHP_EOL);
        }

        $this->userRepository->save(new User($firstName, $lastName, $email));
    }

    private function userExists(string $email)
    {
        try {
            $this->userRepository->findUserByEmail($email);
        } catch (UserNotFoundException $exception)
        {
            return false;
        }
        return true;
    }
}