<?php

namespace Project\Exceptions;

use Exception;

class CommandException extends Exception
{
    public $message = 'No required argument';
}