<?php
namespace Test\Actions;

use Project\Http\Actions\Post\CreatePostAction;
use Project\Http\Response\ErrorResponse;
use Project\Http\Request\Request;
use Project\Http\Response\SuccessfulResponse;
use Project\Exceptions\PostNotFoundException;
use Project\Exceptions\UserNotFoundException;
use Project\Repositories\Post\PostRepositoryInterface;
use Project\Repositories\User\UserRepositoryInterface;
use Project\Blog\Post;
use Project\User\User;
use PHPUnit\Framework\TestCase;

class CreatePostActionTest extends TestCase
{
    /**
    * @runInSeparateProcess
    * @preserveGlobalState disabled
    */
    public function testItReturnsErrorResponseIfNoEmailProvided(): void
    {
        $testBody = array('author_id' => '1', 'text' => 'some text', 'title' => 'some title');
        $request = new Request([], [], json_encode($testBody));
        $postRepository = $this->postRepository([]);
        $userRepository = $this->userRepository([]);
        $action = new CreatePostAction($postRepository, $userRepository);

        $response = $action->handle($request);
        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString('{"success":false,"reason":"No such query param in the request: email"}');
        $response->send();
    }

    private function postRepository(array $posts): PostRepositoryInterface
    {
        return new class($posts) implements PostRepositoryInterface {
            public function __construct(
                private array $posts
            ) {
            }

            public function save(Post $post): void
            {
            }

            public function get(int $id): Post
            {
                throw new PostNotFoundException("Not found");
            }
        };
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
