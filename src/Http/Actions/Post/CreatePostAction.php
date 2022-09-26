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
use Project\Http\Auth\IdentificationInterface;

class CreatePostAction implements ActionInterface
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
        private IdentificationInterface $identification,
        private LoggerInterface $logger,
    ) {
    }

    public function handle(Request $request): Response
    {
        $author = $this->identification->user($request);

        try {
            $post = new Post(
                $author->getId(),
                $request->jsonBodyField('title'),
                $request->jsonBodyField('text'),
            );
        } catch (HttpException $e) {
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