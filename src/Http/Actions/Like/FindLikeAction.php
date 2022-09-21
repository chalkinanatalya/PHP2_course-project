<?php
namespace Project\Http\Actions\Like;

use Project\Http\Actions\ActionInterface;
use Project\Http\Response\ErrorResponse;
use Project\Exceptions\HttpException;
use Project\Http\Request\Request;
use Project\Http\Response\Response;
use Project\Http\Response\SuccessfulResponse;
use Project\Exceptions\PostNotFoundException;
use Project\Repositories\Like\LikeRepositoryInterface;

class FindLikeAction implements ActionInterface
{
    public function __construct(
        private LikeRepositoryInterface $likeRepository
    ) {
    }

    public function handle(Request $request): Response
    {
    try {
        $postId = $request->query('postId');
        $authorId = $request->query('authorId');
    } catch (HttpException $e) {
        return new ErrorResponse($e->getMessage());
    }

    try {
        $like = $this->likeRepository->get($postId, $authorId);
    } catch (PostNotFoundException $e) {
        return new ErrorResponse($e->getMessage());
    }

    return new SuccessfulResponse([
        'post_id' => $like->getPostId(),
        'author_id' => $like->getAuthorId(),
    ]);
    }
}
