<?php
namespace Project\Http\Actions\Post;

use Project\Http\Actions\ActionInterface;
use Project\Http\Response\ErrorResponse;
use Project\Exceptions\HttpException;
use Project\Http\Request\Request;
use Project\Http\Response\Response;
use Project\Http\Response\SuccessfulResponse;
use Project\Exceptions\PostNotFoundException;
use Project\Repositories\Post\PostRepositoryInterface;
use Psr\Log\LoggerInterface;

class FindByIdAction implements ActionInterface
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function handle(Request $request): Response
    {
    try {
        $id = $request->query('id');
    } catch (HttpException $e) {
        return new ErrorResponse($e->getMessage());
    }

    try {
        $post = $this->postRepository->get($id);
    } catch (PostNotFoundException $e) {
        return new ErrorResponse($e->getMessage());
    }

    $this->logger->info("Id found: $id");

    return new SuccessfulResponse([
        'title' => $post->getTitle(),
        'text' => $post->getText(),
    ]);
    }
}
