<?php

namespace Project\Commands\CommentCommand;

use Project\Argument\Argument;

interface CreateCommentCommandInterface
{
    
    public function handle(Argument $argument):void;
}