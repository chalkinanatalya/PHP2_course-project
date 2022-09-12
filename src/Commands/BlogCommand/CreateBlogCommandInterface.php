<?php

namespace Project\Commands\BlogCommand;

use Project\Argument\Argument;

interface CreateBlogCommandInterface
{
    
    public function handle(Argument $argument):void;
}