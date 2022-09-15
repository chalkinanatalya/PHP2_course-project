<?php
namespace Test\Actions;

use Project\Http\Actions\User\FindByEmailAction;
use Project\Http\Response\ErrorResponse;
use Project\Http\Request\Request;
use Project\Http\Response\SuccessfulResponse;
use Project\Exceptions\UserNotFoundException;
use Project\Repositories\User\UserRepositoryInterface;
use Project\User\User;
use PHPUnit\Framework\TestCase;

class FindByEmailActionTest extends TestCase
{
    /**
    * @runInSeparateProcess
    * @preserveGlobalState disabled
    */
    public function testItReturnsErrorResponseIfNoEmailProvided(): void
    {
        $request = new Request([], [], '');
        $userRepository = $this->userRepository([]);
        $action = new FindByEmailAction($userRepository);

        $response = $action->handle($request);
        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString('{"success":false,"reason":"No such query param in the request: email"}');
        $response->send();
    }
    /**
    * @runInSeparateProcess
    * @preserveGlobalState disabled
    */
    public function testItReturnsErrorResponseIfUserNotFound(): void
    {
        // Теперь запрос будет иметь параметр username
        $request = new Request(['email' => 'test@test.com'], [], '');
        $userRepository = $this->userRepository([]);
        $action = new FindByEmailAction($userRepository);

        $response = $action->handle($request);
        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString('{"success":false,"reason":"Not found"}');
        $response->send();
    }
    /**
    * @runInSeparateProcess
    * @preserveGlobalState disabled
    */
    public function testItReturnsSuccessfulResponse(): void
    {
        $request = new Request(['email' => 'test@test.com'], [], '');
        $userRepository = $this->userRepository([new User('Ivan','Ivan','test@test.com'),]);
        $action = new FindByEmailAction($userRepository);
        $response = $action->handle($request);

        $this->assertInstanceOf(SuccessfulResponse::class, $response);
        $this->expectOutputString('{"success":true,"data":{"name":"Ivan Ivan","email":"test@test.com"}}');
        $response->send();
    }

    private function userRepository(array $users): userRepositoryInterface
    {
        return new class($users) implements userRepositoryInterface {
            public function __construct(
                private array $users
            ) {
            }

            public function save(User $user): void
            {
            }

            public function get(int $id): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function findUserByEmail(string $email): User
            {
                foreach ($this->users as $user) {
                    if ($user instanceof User && $email === $user->getEmail())
                    {
                        return $user;
                    }
                }
                throw new UserNotFoundException("Not found");
            }

            public function mapUser(object $userObj): User
            {
                throw new UserNotFoundException("User not found");
            }
        };
    }
}
