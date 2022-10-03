<?php
namespace Test\Actions;

use DateTimeImmutable;
use Project\AuthToken\AuthToken;
use Project\Http\Actions\Post\CreatePostAction;
use Project\Http\Auth\BearerTokenAuthentication;
use Project\Http\Response\ErrorResponse;
use Project\Http\Request\Request;
use Project\Http\Response\SuccessfulResponse;
use Project\Exceptions\PostNotFoundException;
use Project\Exceptions\UserNotFoundException;
use Project\Exceptions\AuthException;
use Project\Repositories\AuthToken\AuthTokenRepository;
use Project\Repositories\Post\PostRepositoryInterface;
use Project\Repositories\User\UserRepositoryInterface;
use Project\Blog\Post\Post;
use Project\Blog\User\User;
use PHPUnit\Framework\TestCase;
use Test\Logs\DummyLogger;
use Project\Http\Auth\JsonBodyIdIdentification;
use Test\RequestTEst;

class CreatePostActionTest extends TestCase
{
    /**
    * @runInSeparateProcess
    * @preserveGlobalState disabled
    */
    public function testItReturnsSuccessfulResponse(): void
    {
        $token = bin2hex(random_bytes(40));
        $tokenRepository = new AuthTokenRepository();
        $tokenRepository->save(
            new AuthToken(
                $token,
                'testy1@tasty.com',
                (new DateTimeImmutable())->modify('+1 day')
            )
        );
        $postRepository = $this->postRepository([]);
        $userRepository = $this->userRepository([]);
        $testBody = array('author_id' => '1', 'text' => 'some text', 'title' => 'some title');
        $request = new RequestTest([], [], json_encode($testBody), 'Bearer '.$token);
        $authentication = new BearerTokenAuthentication(new AuthTokenRepository, $userRepository);
        $action = new CreatePostAction($postRepository, $authentication, new DummyLogger());

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
        $token = bin2hex(random_bytes(40));
        $tokenRepository = new AuthTokenRepository();
        $tokenRepository->save(
            new AuthToken(
                $token,
                'testy1@tasty.com',
                (new DateTimeImmutable())->modify('+1 day')
            )
        );
        $testBody = array('author_id' => 'abc', 'text' => 'some text', 'title' => 'some title');
        $request = new RequestTest([], [], json_encode($testBody), 'Bearer '.$token);
        $postRepository = $this->postRepository([]);
        $userRepository = $this->userRepository([]);
        $authentication = new BearerTokenAuthentication(new AuthTokenRepository, $userRepository);
        $this->expectException(AuthException::class);
        $this->expectExceptionMessage('author_id must be int');

        $action = new CreatePostAction($postRepository, $authentication, new DummyLogger());
        $response = $action->handle($request);
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
        $authentication = new BearerTokenAuthentication(new AuthTokenRepository, $userRepository);
        $this->expectException(AuthException::class);
        $this->expectExceptionMessage('No user with such id');

        $action = new CreatePostAction($postRepository, $authentication, new DummyLogger());
        $response = $action->handle($request);
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
        $authentication = new BearerTokenAuthentication(new AuthTokenRepository, $userRepository);
        $action = new CreatePostAction($postRepository, $authentication, new DummyLogger());

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
        $authentication = new BearerTokenAuthentication(new AuthTokenRepository, $userRepository);
        $this->expectException(AuthException::class);
        $this->expectExceptionMessage('No such field: author_id');

        $action = new CreatePostAction($postRepository, $authentication, new DummyLogger());
        $response = $action->handle($request);
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
            public function getByData(object $post): Post
            {
                throw new PostNotFoundException("Post not found");
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
                    $user = new User('John', 'Doe','testy1@tasty.com', '123');
                    $user->setId(1);
                    return $user;
                } else {
                    throw new UserNotFoundException("No user with such id");
                }
            }

            public function getByEmail(string $email): User
            {
                $user = new User('John', 'Doe','testy1@tasty.com', '123');
                $user->setId(1);
                return $user;
            }

            public function mapUser(object $userObj): User
            {
                throw new UserNotFoundException("User not found");
            }
        };
    }
}
