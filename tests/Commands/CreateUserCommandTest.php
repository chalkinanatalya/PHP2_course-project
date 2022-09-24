<?php
namespace Tests\Commands\UserCommand\CreateUserCommandTest;

use Project\Blog\User\User;
use Project\Commands\UserCommand\CreateUserCommand;
use Project\Exceptions\UserNotFoundException;
use Project\Argument\Argument;
use Project\Exceptions\ArgumentException;
use PHPUnit\Framework\TestCase;
use Project\Repositories\User\UserRepositoryInterface;
use Test\Logs\DummyLogger;

class CreateUserCommandTest extends TestCase
{
    private function makeUserRepository(): UserRepositoryInterface
    {
        return new class implements UserRepositoryInterface
        {
            public function save(User $user): void
            {
            }
            public function get(int $id): User
            {
                throw new UserNotFoundException("User with id: $id not found");
            }
            public function findUserByEmail(string $email): User
            {
                return new User('Ivan', 'Ivanov','test7@test.com');
            }
            public function mapUser(object $userObj): User
            {
                throw new UserNotFoundException("User not found");
            }
        };
    }
    public function testItRequiresLastName(): void
    {
        $command = new CreateUserCommand($this->makeUserRepository(), new DummyLogger());
        $this->expectException(ArgumentException::class);
        $this->expectExceptionMessage('No such argument: lastName');
        $command->handle(new Argument(['firstName' => 'Ivan']));
    }
    public function testItRequiresFirstName(): void
    {
        $command = new CreateUserCommand($this->makeUserRepository(), new DummyLogger());
        $this->expectException(ArgumentException::class);
        $this->expectExceptionMessage('No such argument: firstName');
        $command->handle(new Argument(['lastName' => 'Ivan']));
    }
    public function testItRequiresEmail(): void
    {
        $command = new CreateUserCommand($this->makeUserRepository(), new DummyLogger());
        $this->expectException(ArgumentException::class);
        $this->expectExceptionMessage('No such argument: email');
        $command->handle(new Argument(['lastName' => 'Ivanov','firstName' => 'Ivan']));
    }

    public function testItSavesUserToRepository(): void
    {
        $userRepository = new class implements UserRepositoryInterface {
        private bool $called = false;
        public function save(User $user): void
        {
            $this->called = true;
        }
        public function get(int $id): User
        {
            throw new UserNotFoundException("Not found");
        }
        public function findUserByEmail(string $email): User
        {
            throw new UserNotFoundException("User not found");
        }
        public function mapUser(object $userObj): User
        {
            throw new UserNotFoundException("User not found");
        }
        public function wasCalled(): bool
        {
            return $this->called;
        }
        };

        $command = new CreateUserCommand($userRepository, new DummyLogger());

        $command->handle(new Argument([
            'firstName' => 'Ivan',
            'lastName' => 'Nikitin',
            'email' => 'test9test.com',
        ]));

        $this->assertTrue($userRepository->wasCalled());
    }
}
    