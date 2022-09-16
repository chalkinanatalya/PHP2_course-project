<?php
namespace Project\Http\Actions\User;

use Project\Http\Actions\ActionInterface;
use Project\Http\Response\ErrorResponse;
use Project\Exceptions\HttpException;
use Project\Http\Request\Request;
use Project\Http\Response\Response;
use Project\Http\Response\SuccessfulResponse;
use Project\Exceptions\UserNotFoundException;
use Project\Repositories\User\UserRepositoryInterface;

class FindByEmailAction implements ActionInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function handle(Request $request): Response
    {
    try {
        $email = $request->query('email');
    } catch (HttpException $e) {
        return new ErrorResponse($e->getMessage());
    }

    try {
        $user = $this->userRepository->findUserByEmail($email);
    } catch (UserNotFoundException $e) {
        return new ErrorResponse($e->getMessage());
    }

    return new SuccessfulResponse([
        'name' => $user->getFirstName() . ' ' . $user->getLastName(),
        'email' => $user->getEmail(),
    ]);
    }
}
