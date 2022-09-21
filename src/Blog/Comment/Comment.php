<?php
namespace Project\Blog\Comment;
use Project\Traits\Id;
class Comment implements CommentInterface
{
    use Id;
    public function __construct(
        private int $postId,
        private int $authorId,
        private string $text,
    ) {
    }

    public function getPostId(): int
    {
        return $this->postId;
    }
    public function getAuthorId(): int
    {
        return $this->authorId;
    }
    public function getText(): string
    {
        return $this->text;
    }

    public function __toString()
    {
        return $this->text;
    }
}