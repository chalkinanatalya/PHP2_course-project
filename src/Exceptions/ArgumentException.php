<?php

namespace Project\Exceptions;

use RuntimeException;

class ArgumentException extends RuntimeException
{
    protected $message = 'No required argument';
}