<?php
namespace Project\AuthToken;
use DateTimeImmutable;
class AuthToken
{
    public function __construct(
        private string $token,
        private $email,
        private DateTimeImmutable $expiresOn
    ) {
    }
    public function token(): string
    {
        return $this->token;
    }
    public function email(): string
    {
        return $this->email;
    }
    public function expiresOn(): DateTimeImmutable
    {
        return $this->expiresOn;
    }
}