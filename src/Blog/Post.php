<?php
namespace ChalkinaNatalia\Project\Blog;
use ChalkinaNatalia\Project\Person\User;
class Post
{
    public function __construct(
        private int $id,
        private int $authorId,
        private string $header,
        private string $text,
    ) {
    }
    public function __toString()
    {
        return $this->header . '>>>' . $this->text;
    }
}
