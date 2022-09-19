<?php
namespace Tests\Container;

use PHPUnit\Framework\TestCase;
use Project\Container\DIContainer;
use Project\Exceptions\NotFoundException;
use Tests\Container\SomeClassWithoutDependencies;
use Tests\Container\SomeClassWithParameter;
use Tests\Container\ClassDependingOnAnother;

class DIContainerTest extends TestCase
{
    public function testItThrowsAnExceptionIfCannotResolveType(): void
    {

        $container = new DIContainer();

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(
        'Cannot resolve type: Tests\Container\SomeClass'
        );

        $container->get(SomeClass::class);
    }

    public function testItResolvesClassWithoutDependencies(): void
    {

        $container = new DIContainer();

        $object = $container->get(SomeClassWithoutDependencies::class);
        $this->assertInstanceOf(
            SomeClassWithoutDependencies::class,
            $object
        );
    }

    public function testItReturnsPredefinedObject(): void
    {
        $container = new DIContainer();
        $container->bind(
            SomeClassWithParameter::class,
            new SomeClassWithParameter(42)
        );

        $object = $container->get(SomeClassWithParameter::class);
        $this->assertInstanceOf(
            SomeClassWithParameter::class,
            $object
        );

        $this->assertSame(42, $object->value());
    }

    public function testItResolvesClassWithDependencies(): void
    {
        $container = new DIContainer();
        $container->bind(
            SomeClassWithParameter::class,
            new SomeClassWithParameter(42)
        );
        
        $object = $container->get(ClassDependingOnAnother::class);
        $this->assertInstanceOf(
        ClassDependingOnAnother::class,
        $object
        );
    }

}
