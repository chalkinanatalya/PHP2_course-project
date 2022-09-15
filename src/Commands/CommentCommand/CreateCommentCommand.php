<?php

namespace Project\Commands\CommentCommand;

use Project\Comment\Comment;
use Project\Argument\Argument;
use Project\Repositories\Comment\CommentRepositoryInterface;

class CreateCommentCommand implements CreateCommentCommandInterface
{
    public function __construct(
        private CommentRepositoryInterface $commentRepository,
        )
    {
    }

    public function handle(Argument $argument):void
    {
        $postId = $argument->get('postId');
        $authorId = $argument->get('authorId');
        $text = $argument->get('text');

        $this->commentRepository->save(new Comment($postId, $authorId, $text));
    }


}