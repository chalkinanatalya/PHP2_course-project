<?php
namespace Tests\Commands\CommentCommand\CreateCommentCommandTest;

use Project\Blog\Comment\Comment;
use Project\Commands\CommentCommand\CreateCommentCommand;
use Project\Argument\Argument;
use Project\Exceptions\ArgumentException;
use PHPUnit\Framework\TestCase;
use Project\Repositories\Comment\CommentRepositoryInterface;
use Project\Exceptions\CommentNotFoundException;
use Project\Repositories\Comment\CommentRepository;
use Test\Logs\DummyLogger;

class CreateCommentCommandTest extends TestCase
{
    private function makeCommentRepository(): CommentRepositoryInterface
    {
        return new class implements CommentRepositoryInterface
        {
            public function save(Comment $comment): void
            {
            }
            public function get(int $id): Comment
            {
                throw new CommentNotFoundException("Comment with id: $id not found");
            }
        };
    }
    public function testItRequiresPostId(): void
    {
        $command = new CreateCommentCommand($this->makeCommentRepository(), new DummyLogger());
        $this->expectExceptionMessage('No such argument: postId');
        $command->handle(new Argument(['authorId' => '1', 'text' => 'testText1']));
    }
    public function testItRequiresAuthorId(): void
    {
        $command = new CreateCommentCommand($this->makeCommentRepository(), new DummyLogger());
        $this->expectException(ArgumentException::class);
        $this->expectExceptionMessage('No such argument: authorId');
        $command->handle(new Argument(['postId' => '1', 'text' => 'testText2']));
    }
    public function testItRequiresText(): void
    {
        $command = new CreateCommentCommand($this->makeCommentRepository(), new DummyLogger());
        $this->expectException(ArgumentException::class);
        $this->expectExceptionMessage('No such argument: text');
        $command->handle(new Argument(['postId' => '1','authorId' => '1']));
    }
    public function testItFindComment(): void
    {
        $commentRepository = new CommentRepository(new DummyLogger());

        $testComment = new Comment(1, 1, 'some text');
        $testComment->setId(1);
        $comment = $commentRepository->get(1);
        $this->assertEquals($testComment, $comment);
    }

    public function testItSavesCommentToRepository(): void
    {
        $commentRepository = new class implements CommentRepositoryInterface {
        private bool $called = false;
        public function save(Comment $comment): void
        {
            $this->called = true;
        }
        public function get(int $id): Comment
        {
            throw new CommentNotFoundException("Comment with id: $id not found");
        }
        public function wasCalled(): bool
        {
            return $this->called;
        }
        };

        $command = new CreateCommentCommand($commentRepository, new DummyLogger());

        $command->handle(new Argument([
            'postId' => '1',
            'authorId' => '1',
            'text' => 'testText3',
        ]));

        $this->assertTrue($commentRepository->wasCalled());
    }
}
    