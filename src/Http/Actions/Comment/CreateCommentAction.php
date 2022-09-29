<?php
namespace Project\Http\Actions\Comment;

use Project\Exceptions\ArgumentException;
use Project\Exceptions\UserNotFoundException;
use Project\Http\Actions\ActionInterface;
use Project\Http\Response\ErrorResponse;
use Project\Exceptions\HttpException;
use Project\Http\Request\Request;
use Project\Http\Response\Response;
use Project\Http\Response\SuccessfulResponse;
use Project\Blog\Comment\Comment;
use Project\Repositories\Comment\CommentRepositoryInterface;
use Project\Repositories\Post\PostRepositoryInterface;
use Project\Repositories\User\UserRepositoryInterface;
use Project\Http\Auth\TokenAuthenticationInterface;

class CreateCommentAction implements ActionInterface
{
    public function __construct(
        private CommentRepositoryInterface $commentRepository,
        private PostRepositoryInterface $postRepository,
        private UserRepositoryInterface $userRepository,
        private TokenAuthenticationInterface $authentication,
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $authorId = $request->jsonBodyField('author_id');
        } catch (HttpException | ArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }
        
        if (ctype_alpha($authorId)) {
            return new ErrorResponse('author_id must be int');
        }

        try {
            $this->userRepository->get($authorId);
        } catch (UserNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $comment = new Comment(
                $request->jsonBodyField('post_id'),
                $authorId,
                $request->jsonBodyField('text'),
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }
        
        $this->commentRepository->save($comment);

        return new SuccessfulResponse([
            'postId' => $comment->getPostId(),
            'text' => $comment->getText(),
        ]);
    }
}