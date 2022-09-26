<?php
namespace Project\Http\Auth;

use Project\Http\Auth\IdentificationInterface;
use Project\Repositories\User\UserRepositoryInterface;
use Project\Exceptions\HttpException;
use Project\Exceptions\ArgumentException;
use Project\Exceptions\AuthException;
use Project\Exceptions\UserNotFoundException;
use Project\Http\Request\Request;
use Project\Blog\User\User;
use Project\Http\Response\ErrorResponse;

class JsonBodyIdIdentification implements IdentificationInterface
{
    public function __construct(
    private UserRepositoryInterface $userRepository
    ) {
    }
    public function user(Request $request): User
    {
        try {
            $authorId = $request->jsonBodyField('author_id');
        } catch (HttpException|ArgumentException $e) {
            throw new AuthException($e->getMessage());
        }
        
        if (ctype_alpha($authorId)) {
            throw new AuthException('author_id must be int');
        }

        try {
            return $this->userRepository->get($authorId);
        } catch (UserNotFoundException $e) {
            throw new AuthException($e->getMessage());
        }
    }
}
