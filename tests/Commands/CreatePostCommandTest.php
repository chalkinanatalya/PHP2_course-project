<?php
namespace Tests\Commands\PostCommand\CreatePostCommandTest;

use Project\Blog\Post\Post;
use Project\Commands\PostCommand\CreatePostCommand;
use Project\Argument\Argument;
use Project\Exceptions\ArgumentException;
use PHPUnit\Framework\TestCase;
use Project\Repositories\Post\PostRepositoryInterface;
use Project\Exceptions\PostNotFoundException;
use Project\Repositories\Post\PostRepository;
use Test\Logs\DummyLogger;

class CreatePostCommandTest extends TestCase
{
    private function makePostRepository(): PostRepositoryInterface
    {
        return new class implements PostRepositoryInterface
        {
            public function save(Post $post): void
            {
            }
            public function get(int $id): Post
            {
                throw new PostNotFoundException("Post with id: $id not found");
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
    public function testItRequiresUserId(): void
    {
        $command = new CreatePostCommand($this->makePostRepository(), new DummyLogger());
        $this->expectExceptionMessage('No such argument: userId');
        $command->handle(new Argument(['title' => 'testTitle1', 'text' => 'testText1']));
    }
    public function testItRequiresTitle(): void
    {
        $command = new CreatePostCommand($this->makePostRepository(), new DummyLogger());
        $this->expectException(ArgumentException::class);
        $this->expectExceptionMessage('No such argument: title');
        $command->handle(new Argument(['userId' => '1', 'text' => 'testText2']));
    }
    public function testItRequiresText(): void
    {
        $command = new CreatePostCommand($this->makePostRepository(), new DummyLogger());
        $this->expectException(ArgumentException::class);
        $this->expectExceptionMessage('No such argument: text');
        $command->handle(new Argument(['userId' => '1','title' => 'testTitle2']));
    }
    public function testItFindPost(): void
    {
        $postRepository = new PostRepository(new DummyLogger());

        $testPost = new Post(6, 'some title', 'some text');
        $testPost->setId(2);
        $post = $postRepository->get(2);
        $this->assertEquals($testPost, $post);
    }
    public function testItSavesPostToRepository(): void
    {
        $postRepository = new class implements PostRepositoryInterface {
        private bool $called = false;
        public function save(Post $post): void
        {
            $this->called = true;
        }
        public function get(int $id): Post
        {
            throw new PostNotFoundException("Post with id: $id not found");
        }
        public function delete(int $id): void
        {
        }
        public function getByData(object $post): Post
        {
            throw new PostNotFoundException("Post not found");
        }
        public function wasCalled(): bool
        {
            return $this->called;
        }
        };

        $command = new CreatePostCommand($postRepository, new DummyLogger());

        $command->handle(new Argument([
            'userId' => '1',
            'title' => 'testTitle3',
            'text' => 'testText3',
        ]));

        $this->assertTrue($postRepository->wasCalled());
    }
}
    