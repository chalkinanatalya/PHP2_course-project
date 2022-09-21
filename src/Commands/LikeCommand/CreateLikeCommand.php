<?php

namespace Project\Commands\LikeCommand;

use Project\Blog\Like\Like;
use Project\Argument\Argument;
use Project\Repositories\Like\LikeRepositoryInterface;

class CreateLikeCommand implements CreateLikeCommandInterface
{
    public function __construct(
        private LikeRepositoryInterface $likeRepository,
        )
    {
    }

    public function handle(Argument $argument):void
    {
        $postId = $argument->get('postId');
        $authorId = $argument->get('authorId');

        $this->likeRepository->save(new Like($postId, $authorId));
    }


}