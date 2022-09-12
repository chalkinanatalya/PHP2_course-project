<?php

namespace Project\Commands\BlogCommand;

use Project\Blog\Post;
use Project\Argument\Argument;
use Project\Commands\BlogCommand\CreateBlogCommandInterface;
use Project\Repositories\Post\PostRepositoryInterface;

class CreateBlogCommand implements CreateBlogCommandInterface
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
        )
    {
    }

    public function handle(Argument $argument):void
    {
        $userId = $argument->get('userId');
        $title = $argument->get('title');
        $text = $argument->get('text');

        $this->postRepository->save(new Post($userId, $title, $text));
    }


}