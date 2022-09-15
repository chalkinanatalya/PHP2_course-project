<?php
namespace Project\Http\Actions\Post;

use Project\Exceptions\ArgumentException;
use Project\Exceptions\UserNotFoundException;
use Project\Http\Actions\ActionInterface;
use Project\Http\Response\ErrorResponse;
use Project\Exceptions\HttpException;
use Project\Http\Request\Request;
use Project\Http\Response\Response;
use Project\Http\Response\SuccessfulResponse;
use Project\Blog\Post;
use Project\Repositories\Post\PostRepositoryInterface;
use Project\Repositories\User\UserRepositoryInterface;

class CreatePostAction implements ActionInterface
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
        private UserRepositoryInterface $userRepository,
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $authorId = $request->jsonBodyField('author_id');
        } catch (HttpException | ArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }
        
        try {
            $this->userRepository->get($authorId);
        } catch (UserNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
        $post = new Post(
            $authorId,
            $request->jsonBodyField('title'),
            $request->jsonBodyField('text'),
        );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }
        
        $this->postRepository->save($post);

        return new SuccessfulResponse([
            'title' => $post->getTitle(),
            'text' => $post->getText(),
        ]);
    }
}