<?php
namespace Tests\Container;

use Tests\Container\SomeClassWithoutDependencies;
use Tests\Container\SomeClassWithParameter;

class ClassDependingOnAnother
{
    public function __construct(
        private SomeClassWithoutDependencies $one,
        private SomeClassWithParameter $two,
    ) {
    }
}