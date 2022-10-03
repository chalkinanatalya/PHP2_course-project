<?php

namespace Project\Repositories\Post;

use Project\Blog\Post\Post;

interface PostRepositoryInterface
{
    public function save(Post $post): void;
    public function get(int $id): Post;
    public function delete(int $id): void;
    public function getByData(object $post): Post;
}