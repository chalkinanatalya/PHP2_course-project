<?php

namespace Project\Commands\UserCommand;

use Project\Exceptions\CommandException;
use Project\Exceptions\UserNotFoundException;
use Project\Repositories\User\UserRepositoryInterface;
use Project\Blog\User\User;
use Project\Argument\Argument;
use Psr\Log\LoggerInterface;

class CreateUserCommand implements CreateUserCommandInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private LoggerInterface $logger,
        )
    {
    }

    public function handle(Argument $argument):void
    {
        $this->logger->info("Create user command started");
        $firstName = $argument->get('firstName');
        $lastName = $argument->get('lastName');
        $email = $argument->get('email');

        if($this->userExists($email))
        {
            $this->logger->warning("User already exists: $email");
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