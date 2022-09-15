<?php

namespace Project\Repositories\Post;

use Project\Blog\Post;

interface PostRepositoryInterface
{
    public function save(Post $post): void;
    public function get(int $id): Post;
}