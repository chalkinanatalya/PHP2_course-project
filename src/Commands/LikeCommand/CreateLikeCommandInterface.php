<?php

namespace Project\Commands\LikeCommand;

use Project\Argument\Argument;

interface CreateLikeCommandInterface
{
    
    public function handle(Argument $argument):void;
}