<?php

namespace Project\Exceptions;

use Exception;

class CommentNotFoundException extends Exception
{
    protected $message = 'Comment not found';
}