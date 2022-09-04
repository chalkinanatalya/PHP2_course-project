<?php
namespace ChalkinaNatalia\Project\Comment;
use ChalkinaNatalia\Project\Person\User;
class Comment
{
    public function __construct(
        private int $id,
        private int $authorId,
        private int $postId,
        private string $textComment,

    ) {
    }

    public function __toString()
    {
        return $this->textComment;
    }
}