<?php
namespace Project\Http\Auth;

use Project\Repositories\AuthToken\AuthTokenRepositoryInterface;
use Project\Repositories\User\UserRepositoryInterface;
use Project\Exceptions\HttpException;
use Project\Exceptions\AuthException;
use Project\Exceptions\AuthTokenNotFoundException;
use DateTimeImmutable;
use Project\Blog\User\User;
use Project\Http\Request\Request;

class BearerTokenAuthentication implements TokenAuthenticationInterface
{
    private const HEADER_PREFIX = 'Bearer ';
    public function __construct(
        private AuthTokenRepositoryInterface $authTokensRepository,
        private UserRepositoryInterface $userRepository,
    ) {
    }

    public function user(Request $request): User
    {
        try {
            $header = $request->header('Authorization');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }
        
        if (!str_starts_with($header, self::HEADER_PREFIX)) {
            throw new AuthException("Malformed token: [$header]");
        }
        
        $token = mb_substr($header, strlen(self::HEADER_PREFIX));
        try {
            $authToken = $this->authTokensRepository->get($token);
        } catch (AuthTokenNotFoundException) {
            throw new AuthException("Bad token: [$token]");
        }
        if ($authToken->expiresOn() <= new DateTimeImmutable()) {
            throw new AuthException("Token expired: [$token]");
        }

        $email = $authToken->email();
        return $this->userRepository->getByEmail($email);
    }
}