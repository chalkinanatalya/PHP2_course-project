<?php
namespace Project\Http\Actions\Like;

use Project\Exceptions\ArgumentException;
use Project\Exceptions\UserNotFoundException;
use Project\Exceptions\PostNotFoundException;
use Project\Exceptions\LikeNotFoundException;
use Project\Http\Actions\ActionInterface;
use Project\Http\Response\ErrorResponse;
use Project\Exceptions\HttpException;
use Project\Http\Request\Request;
use Project\Http\Response\Response;
use Project\Http\Response\SuccessfulResponse;
use Project\Blog\Like\Like;
use Project\Repositories\Post\PostRepositoryInterface;
use Project\Repositories\User\UserRepositoryInterface;
use Project\Repositories\Like\LikeRepositoryInterface;

class CreateLikeAction implements ActionInterface
{
    public function __construct(
        private LikeRepositoryInterface $likeRepository,
        private PostRepositoryInterface $postRepository,
        private UserRepositoryInterface $userRepository,
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $authorId = $request->jsonBodyField('author_id');
            $postId = $request->jsonBodyField('post_id');
        } catch (HttpException | ArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }

        if (ctype_alpha($authorId)) {
            return new ErrorResponse('author_id must be the int');
        }
        if (ctype_alpha($postId)) {
            return new ErrorResponse('post_id must be the int');
        }

        try {
            $this->userRepository->get($authorId);
        } catch (UserNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $this->postRepository->get($postId);
        } catch (PostNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $this->likeRepository->get($postId, $authorId);
            return new ErrorResponse('Like already exists');
        } catch (LikeNotFoundException $e) {
        }

        try {
            $like = new Like(
                $postId,
                $authorId
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->likeRepository->save($like);

        return new SuccessfulResponse([
            'post_id' => $like->getPostId(),
            'author_id' => $like->getAuthorId(),
        ]);
    }
}