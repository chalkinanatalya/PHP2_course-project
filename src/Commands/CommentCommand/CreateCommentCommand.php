<?php

namespace Project\Commands\CommentCommand;

use Project\Blog\Comment\Comment;
use Project\Argument\Argument;
use Project\Repositories\Comment\CommentRepositoryInterface;
use Psr\Log\LoggerInterface;

class CreateCommentCommand implements CreateCommentCommandInterface
{
    public function __construct(
        private CommentRepositoryInterface $commentRepository,
        private LoggerInterface $logger,
        )
    {
    }

    public function handle(Argument $argument):void
    {
        $this->logger->info("Create comment command started");
        $postId = $argument->get('postId');
        $authorId = $argument->get('authorId');
        $text = $argument->get('text');

        $this->commentRepository->save(new Comment($postId, $authorId, $text));
    }


}