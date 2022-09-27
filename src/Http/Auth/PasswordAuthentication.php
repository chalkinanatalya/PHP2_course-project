<?php
namespace Project\Http\Auth;
use Project\Http\Request\Request;
use Project\Blog\User\User;
use Project\Repositories\User\UserRepositoryInterface;
use Project\Exceptions\HttpException;
use Project\Exceptions\AuthException;
use Project\Exceptions\UserNotFoundException;

class PasswordAuthentication implements PasswordAuthenticationInterface
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
            $user = $this->userRepository->getByEmail($email);
            } catch (UserNotFoundException $e) {
            throw new AuthException($e->getMessage());
        }

        try {
            $password = $request->jsonBodyField('password');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        if (!$user->checkPassword($password)) 
        {
            throw new AuthException('Wrong password');
        }

        return $user;
    }
}