<?php
namespace Project\Http\Auth;

use Project\Http\Request\Request;
use Project\Blog\User\User;
interface IdentificationInterface
{
    public function user(Request $request): User;
}