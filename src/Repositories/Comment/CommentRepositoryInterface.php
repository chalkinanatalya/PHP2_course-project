<?php

namespace Project\Repositories\Comment;

use Project\Blog\Comment\Comment;

interface CommentRepositoryInterface
{
    public function save(Comment $comment): void;
    public function get(int $id): Comment;
}