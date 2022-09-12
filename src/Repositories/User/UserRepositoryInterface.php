<?php

namespace Project\Repositories\User;

use Project\User\User;
interface UserRepositoryInterface
{
    public function save(User $user): void;
    public function get(int $id): User;
    public function findUserByEmail(string $email): User;
    public function mapUser(object $userObj):User;
}