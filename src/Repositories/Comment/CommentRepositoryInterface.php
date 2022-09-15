<?php

namespace Project\Repositories\Comment;

use Project\Comment\Comment;

interface CommentRepositoryInterface
{
    public function save(Comment $comment): void;
    public function get(int $id): Comment;
}