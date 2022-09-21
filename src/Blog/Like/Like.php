<?php
namespace Project\Blog\Like;
use Project\Traits\Id;
class Like implements LikeInterface
{
    use Id;
    public function __construct(
        private int $postId,
        private int $authorId,
    ) {
    }

    public function getPostId(): string
    {
        return $this->postId;
    }
    public function getAuthorId(): int
    {
        return $this->authorId;
    }

    public function __toString()
    {
        return 'post: ' . $this->postId . ', author: ' . $this->authorId;
    }
}
