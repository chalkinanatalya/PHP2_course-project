<?php

namespace Project\Commands\PostCommand;

use Project\Blog\Post\Post;
use Project\Argument\Argument;
use Project\Commands\PostCommand\CreatePostCommandInterface;
use Project\Repositories\Post\PostRepositoryInterface;
use Psr\Log\LoggerInterface;

class CreatePostCommand implements CreatePostCommandInterface
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
        private LoggerInterface $logger,
    )
    {
    }

    public function handle(Argument $argument):void
    {
        $this->logger->info("Create post command started");
        $userId = $argument->get('userId');
        $title = $argument->get('title');
        $text = $argument->get('text');

        $this->postRepository->save(new Post($userId, $title, $text));
    }


}