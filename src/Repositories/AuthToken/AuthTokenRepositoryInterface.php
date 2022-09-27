<?php
namespace Project\Repositories\AuthToken;

use Project\AuthToken\AuthToken;
interface AuthTokenRepositoryInterface
{
    public function save(AuthToken $authToken): void;
    public function get(string $token): AuthToken;
}