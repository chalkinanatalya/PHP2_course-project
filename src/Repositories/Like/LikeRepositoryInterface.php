<?php

namespace Project\Repositories\Like;

use Project\Blog\Like\Like;

interface LikeRepositoryInterface
{
    public function save(Like $comment): void;
    public function get(int $postId, int $authorId): Like;
}