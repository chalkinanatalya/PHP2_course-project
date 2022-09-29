<?php
namespace Project\Http\Auth;

use Project\Repositories\User\UserRepositoryInterface;
use Project\Http\Request\Request;
use Project\Blog\User\User;
use Project\Exceptions\HttpException;
use Project\Exceptions\AuthException;
use Project\Exceptions\UserNotFoundException;

class JsonBodyEmailIdentification implements AuthenticationInterface
{
    public function __construct(
    private UserRepositoryInterface $userRepository
    ) {
    }
    public function user(Request $request): User
    {
        try {
            $email = $request->jsonBodyField('email');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }
        try {
            return $this->userRepository->getByEmail($email);
        } catch (UserNotFoundException $e) {
            throw new AuthException($e->getMessage());
        }
    }
}
    