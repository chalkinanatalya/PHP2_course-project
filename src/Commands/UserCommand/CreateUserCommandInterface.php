<?php

namespace Project\Commands\UserCommand;

use Project\Argument\Argument;

interface CreateUserCommandInterface
{
    
    public function handle(Argument $argument):void;
}