<?php
namespace Project\Http\Actions\Auth;

use Project\Http\Auth\PasswordAuthenticationInterface;
use Project\Http\Actions\ActionInterface;
use Project\Repositories\AuthToken\AuthTokenRepositoryInterface;
use Project\Http\Request\Request;
use Project\Http\Response\Response;
use Project\Exceptions\AuthException;
use Project\Http\Response\ErrorResponse;
use Project\AuthToken\AuthToken;
use DateTimeImmutable;
use Project\Http\Response\SuccessfulResponse;

class LogIn implements ActionInterface
{
    public function __construct(
        private PasswordAuthenticationInterface $passwordAuthentication,
        private AuthTokenRepositoryInterface $authTokenRepository
    ) {
    }
    public function handle(Request $request): Response
    {
        try {
            $user = $this->passwordAuthentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }
        
        $authToken = new AuthToken(
            bin2hex(random_bytes(40)),
            $user->getEmail(),
            (new DateTimeImmutable())->modify('+1 day')
        );

        $this->authTokenRepository->save($authToken);
        
        return new SuccessfulResponse([
            'token' => (string)$authToken->token(),
        ]);
    }
}