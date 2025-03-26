<?php

namespace Tests\Unit;

use App\Tools\Attributes\ToolMethod;
use App\Tools\Attributes\ToolParameter;
use App\Tools\JsonSchema\JsonSchema;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class ToolMethodTest extends TestCase
{
    public function test_that_it_handles_multiple_types(): void
    {
        $o = new class {
            #[ToolMethod('myTool')]
            public function myTool(
                #[ToolParameter('myInt')] int $a,
                #[ToolParameter('myFloat')] float $b,
                #[ToolParameter('myString')] string $c,
                #[ToolParameter('myBool')] bool $d
            ) {
            }
        };

        $this->assertEquals(
            json_encode(['type' => 'object', 'properties' => [
                'a' => ['type' => 'number', 'description' => 'myInt'],
                'b' => ['type' => 'number', 'description' => 'myFloat'],
                'c' => ['type' => 'string', 'description' => 'myString'],
                'd' => ['type' => 'boolean', 'description' => 'myBool'],
            ], 'required' => ['a', 'b', 'c', 'd']]),
            json_encode(JsonSchema::fromMethod(new ReflectionMethod($o, 'myTool')))
        );
    }
}
