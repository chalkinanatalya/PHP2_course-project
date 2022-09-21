<?php

namespace Project\Commands\PostCommand;

use Project\Argument\Argument;

interface CreatePostCommandInterface
{
    
    public function handle(Argument $argument):void;
}