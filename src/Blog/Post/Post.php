<?php
namespace Project\Blog\Post;
use Project\Traits\Id;
class Post implements PostInterface
{
    use Id;
    public function __construct(
        private int $authorId,
        private string $title,
        private string $text,
    ) {
    }

    public function getTitle(): string
    {
        return $this->title;
    }
    public function getText(): string
    {
        return $this->text;
    }
    public function getAuthorId(): int
    {
        return $this->authorId;
    }

    public function __toString()
    {
        return $this->title . '>>>' . $this->text;
    }
}
