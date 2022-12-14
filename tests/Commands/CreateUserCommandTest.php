<?php
namespace Tests\Commands\UserCommand\CreateUserCommandTest;

use Project\Blog\User\User;
use Project\Commands\UserCommand\CreateUserCommand;
use Project\Console\User\CreateUser;
use Project\Exceptions\UserNotFoundException;
use Project\Argument\Argument;
use PHPUnit\Framework\TestCase;
use Project\Repositories\User\UserRepositoryInterface;
use Test\Logs\DummyLogger;

use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

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
            public function getByEmail(string $email): User
            {
                return new User('Ivan', 'Ivanov','test7@test.com', '1234');
            }
            public function mapUser(object $userObj): User
            {
                throw new UserNotFoundException("User not found");
            }
        };
    }
    public function testItRequiresLastName(): void
    {
        $command = new CreateUser($this->makeUserRepository());
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "lastName").');
        $command->run(
            new ArrayInput([
                'firstName' => 'Ivan',
                'email' => 'test9test.com',
                'password' => 'some_password',
            ]),
            new NullOutput()
        );
    }
    public function testItRequiresFirstName(): void
    {
        $command = new CreateUser($this->makeUserRepository());
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "firstName").');
        $command->run(
            new ArrayInput([
                'lastName' => 'Ivan',
                'email' => 'test9test.com',
                'password' => 'some_password',
            ]),
            new NullOutput()
        );
    }
    public function testItRequiresEmail(): void
    {
        $command = new CreateUser($this->makeUserRepository());
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "email").');
        $command->run(
            new ArrayInput([
                'firstName' => 'Ivan',
                'lastName' => 'Ivanov',
                'password' => 'some_password',
            ]),
            new NullOutput()
        );
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
        public function getByEmail(string $email): User
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
            'password' => '1234',
        ]));

        $this->assertTrue($userRepository->wasCalled());
    }

    public function testItRequiresPassword(): void
    {
        $command = new CreateUser($this->makeUserRepository());
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "password").');
        $command->run(
            new ArrayInput([
                'firstName' => 'Ivan',
                'lastName' => 'Ivanov',
                'email' => 'test9@test.com',
            ]),
            new NullOutput()
        );
    }
}


    