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
    public function testItReturnsSuccessfulResponse(): void
    {
        $testBody = array('author_id' => "1", 'text' => 'some text', 'title' => 'some title');
        $request = new Request([], [], json_encode($testBody));
        $postRepository = $this->postRepository([]);
        $userRepository = $this->userRepository([]);
        $action = new CreatePostAction($postRepository, $userRepository);

        $response = $action->handle($request);
        $this->assertInstanceOf(SuccessfulResponse::class, $response);
        $this->expectOutputString('{"success":true,"data":{"title":"some title","text":"some text"}}');
        $response->send();
    }
    /**
    * @runInSeparateProcess
    * @preserveGlobalState disabled
    */
    public function testItReturnsErrorResponseIfIdIsWrong(): void
    {
        $testBody = array('author_id' => 'abc', 'text' => 'some text', 'title' => 'some title');
        $request = new Request([], [], json_encode($testBody));
        $postRepository = $this->postRepository([]);
        $userRepository = $this->userRepository([]);
        $action = new CreatePostAction($postRepository, $userRepository);

        $response = $action->handle($request);
        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString('{"success":false,"reason":"author_id must be int"}');
        $response->send();
    }
    /**
    * @runInSeparateProcess
    * @preserveGlobalState disabled
    */
    public function testItReturnsErrorResponseIfUserNotFound(): void
    {
        $testBody = array('author_id' => "2", 'text' => 'some text', 'title' => 'some title');
        $request = new Request([], [], json_encode($testBody));
        $postRepository = $this->postRepository([]);
        $userRepository = $this->userRepository([]);
        $action = new CreatePostAction($postRepository, $userRepository);

        $response = $action->handle($request);
        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString('{"success":false,"reason":"No user with such id"}');
        $response->send();
    }
    /**
    * @runInSeparateProcess
    * @preserveGlobalState disabled
    */
    public function testItReturnsErrorResponseIfNoTitleProvided(): void
    {
        $testBody = array('author_id' => "1", 'text' => 'some text');
        $request = new Request([], [], json_encode($testBody));
        $postRepository = $this->postRepository([]);
        $userRepository = $this->userRepository([]);
        $action = new CreatePostAction($postRepository, $userRepository);

        $response = $action->handle($request);
        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString('{"success":false,"reason":"No such field: title"}');
        $response->send();
    }
    /**
    * @runInSeparateProcess
    * @preserveGlobalState disabled
    */
    public function testItReturnsErrorResponseIfNoAuthorIdProvided(): void
    {
        $testBody = array('text' => 'some text', 'title' => 'some title');
        $request = new Request([], [], json_encode($testBody));
        $postRepository = $this->postRepository([]);
        $userRepository = $this->userRepository([]);
        $action = new CreatePostAction($postRepository, $userRepository);

        $response = $action->handle($request);
        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString('{"success":false,"reason":"No such field: author_id"}');
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
                throw new PostNotFoundException("Post not found");
            }

            public function delete(int $id): void
            {
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
                if($id === 1) {
                    return new User('Ivan', 'Ivanov','test1@test.com');
                } else {
                    throw new UserNotFoundException("No user with such id");
                }
            }

            public function findUserByEmail(string $email): User
            {
                foreach ($this->users as $user) {
                    if ($user instanceof User && $email === $user->getEmail())
                    {
                        return $user;
                    }
                }
                throw new UserNotFoundException("User with email not found");
            }

            public function mapUser(object $userObj): User
            {
                throw new UserNotFoundException("User not found");
            }
        };
    }
}
