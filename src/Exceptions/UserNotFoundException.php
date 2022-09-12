<?php
namespace Project\Exceptions;

use Exception;
class UserNotFoundException extends Exception
{
    public $message = 'User not found';
}