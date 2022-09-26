<?php

namespace Project\Commands\LikeCommand;

use Project\Blog\Like\Like;
use Project\Argument\Argument;
use Project\Repositories\Like\LikeRepositoryInterface;
use Psr\Log\LoggerInterface;

class CreateLikeCommand implements CreateLikeCommandInterface
{
    public function __construct(
        private LikeRepositoryInterface $likeRepository,
        private LoggerInterface $logger,
        )
    {
    }

    public function handle(Argument $argument):void
    {
        $this->logger->info("Create like command started");
        $postId = $argument->get('postId');
        $authorId = $argument->get('authorId');

        $this->likeRepository->save(new Like($postId, $authorId));
    }


}