<?php

namespace Project\Exceptions;

use Exception;

class LikeNotFoundException extends Exception
{
    protected $message = 'Like not found';
}