<?php

namespace Tests\Unit;

use App\Tools\Attributes\ToolFunction;
use App\Tools\Attributes\ToolParameter;
use App\Tools\JsonSchema\JsonSchema;
use PHPUnit\Framework\TestCase;
use ReflectionFunction;

class ToolFunctionTest extends TestCase
{
    public function test_that_it_handles_functions(): void
    {
        $jsonSchema = new JsonSchema();

        $this->assertEquals(
            json_encode(['type' => 'object', 'properties' => [
                'a' => ['type' => 'number', 'description' => 'This is of type int'],
                'b' => ['type' => 'number', 'description' => 'This is of type float'],
                'c' => ['type' => 'string', 'description' => 'This is of type string'],
                'd' => ['type' => 'boolean', 'description' => 'This is of type bool'],
            ], 'required' => ['a', 'b', 'c', 'd']]),
            json_encode($jsonSchema->fromFunction(new ReflectionFunction('\\Tests\\testTool')))
        );
    }

    public function test_that_it_handles_lambda_functions(): void
    {
        $jsonSchema = new JsonSchema();

        $f = #[ToolFunction('testTool')]
            function (
                #[ToolParameter('This is of type int')] int $a,
                #[ToolParameter('This is of type float')] float $b,
                #[ToolParameter('This is of type string')] string $c,
                #[ToolParameter('This is of type bool')] bool $d
            ) {
            };

        $this->assertEquals(
            json_encode(['type' => 'object', 'properties' => [
                'a' => ['type' => 'number', 'description' => 'This is of type int'],
                'b' => ['type' => 'number', 'description' => 'This is of type float'],
                'c' => ['type' => 'string', 'description' => 'This is of type string'],
                'd' => ['type' => 'boolean', 'description' => 'This is of type bool'],
            ], 'required' => ['a', 'b', 'c', 'd']]),
            json_encode($jsonSchema->fromFunction(new ReflectionFunction($f)))
        );
    }
}
