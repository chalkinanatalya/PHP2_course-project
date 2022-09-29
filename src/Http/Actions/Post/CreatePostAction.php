<?php
namespace Project\Http\Actions\Post;

use Project\Http\Actions\ActionInterface;
use Project\Http\Response\ErrorResponse;
use Project\Exceptions\HttpException;
use Project\Http\Request\Request;
use Project\Http\Response\Response;
use Project\Http\Response\SuccessfulResponse;
use Project\Blog\Post\Post;
use Project\Repositories\Post\PostRepositoryInterface;
use Psr\Log\LoggerInterface;
use Project\Exceptions\AuthException;
use Project\Http\Auth\TokenAuthenticationInterface;

class CreatePostAction implements ActionInterface
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
        private TokenAuthenticationInterface $authentication,
        private LoggerInterface $logger,
    ) {
    }

    public function handle(Request $request): Response
    {
        $author = $this->authentication->user($request);
        if (strval($author->getId()) !== $request->jsonBodyField('author_id'))
        {
            return new ErrorResponse('Authorization error');
        }

        try {
            $post = new Post(
                $author->getId(),
                $request->jsonBodyField('title'),
                $request->jsonBodyField('text'),
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $user = $this->authentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }
        
        $this->postRepository->save($post);
        $this->logger->info("Post created: $post");

        return new SuccessfulResponse([
            'title' => $post->getTitle(),
            'text' => $post->getText(),
        ]);
    }
}