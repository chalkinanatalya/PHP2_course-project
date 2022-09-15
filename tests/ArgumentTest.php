<?php
namespace Test\ArgumentTest;

use PHPUnit\Framework\TestCase;
use Project\Argument\Argument;
use Project\Exceptions\ArgumentException;
final class ArgumentTest extends TestCase
{
    public function testItReturnsArgumentValueName(): void
    {

        $argument = new Argument(['some_key' => 'some_value']);
        $value = $argument->get('some_key');

        $this->assertSame('some_value', $value);
    }

    public function testItThrowAnExceptionWhenArgumentIsAbsent(): void
    {
        $argument = new Argument([]);

        $this->expectException(ArgumentException::class);

        $this->expectExceptionMessage("No such argument: some_key");
        
        $argument->get('some_key');
    }

    public function argumentsProvider(): iterable
    {
    return [
        ['some_string', 'some_string'],
        ['some_string', 'some_string'], 
        ['some_string ', 'some_string'],
        [123, '123'],
        [12.3, '12.3'],
    ];
    }

    /**
    * @dataProvider argumentsProvider
    */
    public function testItConvertsArgumentsToStrings(
        $inputValue,
        $expectedValue
        ): void {

        $arguments = new Argument(['some_key' => $inputValue]);
        $value = $arguments->get('some_key');

        $this->assertEquals($expectedValue, $value);
        }
}