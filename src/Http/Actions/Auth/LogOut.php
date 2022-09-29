<?php
namespace Project\Http\Actions\Auth;

use Project\Http\Actions\ActionInterface;
use Project\Repositories\AuthToken\AuthTokenRepositoryInterface;
use Project\Http\Request\Request;
use Project\Http\Response\Response;
use Project\AuthToken\AuthToken;
use DateTimeImmutable;
use Project\Http\Response\SuccessfulResponse;

class LogOut implements ActionInterface
{
    public function __construct(
        private AuthTokenRepositoryInterface $authTokenRepository
    ) {
    }
    public function handle(Request $request): Response
    {
        $token = new AuthToken(
            $request->jsonBodyField('token'),
            $request->jsonBodyField('email'),
            new DateTimeImmutable(),
        );        

        $this->authTokenRepository->save($token);
        
        return new SuccessfulResponse([
            'Logout successfully',
        ]);
    }
}