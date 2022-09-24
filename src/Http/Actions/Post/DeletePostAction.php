<?php
namespace Project\Http\Actions\Post;

use Project\Http\Actions\ActionInterface;
use Project\Http\Response\ErrorResponse;
use Project\Exceptions\HttpException;
use Project\Http\Request\Request;
use Project\Http\Response\Response;
use Project\Http\Response\SuccessfulResponse;
use Project\Repositories\Post\PostRepositoryInterface;
use Psr\Log\LoggerInterface;

class DeletePostAction implements ActionInterface
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $postId = $request->query('id');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }
        
        $this->postRepository->delete($postId);
        $this->logger->info("Post deleted: $postId");

        return new SuccessfulResponse([
            'status' => 'post with id: ' . $postId . 'has been deleted'
        ]);
    }
}