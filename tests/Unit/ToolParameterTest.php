<?php

namespace Tests\Unit;

use App\Tools\Attributes\ToolParameter;
use App\Tools\JsonSchema\JsonSchema;
use App\Tools\JsonSchema\JsonSchemaArray;
use App\Tools\JsonSchema\JsonSchemaUnion;
use Closure;
use PHPUnit\Framework\TestCase;
use ReflectionFunction;

class ToolParameterTest extends TestCase
{
    private function fromFunction(Closure $function): object
    {
        $jsonSchema = new JsonSchema();

        return $jsonSchema->fromFunction(new ReflectionFunction($function));
    }

    public function test_that_it_handles_multiple_types(): void
    {
        $this->assertEquals(
            json_encode(['type' => 'object', 'properties' => [
                'a' => ['type' => 'number', 'description' => 'myInt'],
                'b' => ['type' => 'number', 'description' => 'myFloat'],
                'c' => ['type' => 'string', 'description' => 'myString'],
                'd' => ['type' => 'boolean', 'description' => 'myBool'],
            ], 'required' => ['a', 'b', 'c', 'd']]),
            json_encode($this->fromFunction(fn (
                #[ToolParameter('myInt')] int $a,
                #[ToolParameter('myFloat')] float $b,
                #[ToolParameter('myString')] string $c,
                #[ToolParameter('myBool')] bool $d
            ) => null))
        );
    }

    public function test_that_it_handles_arrays_of_basic_types(): void
    {
        $this->assertEquals(
            json_encode(['type' => 'object', 'properties' => ['x' => ['type' => 'array', 'items' => ['type' => 'string'], 'description' => 'desc']], 'required' => ['x']]),
            json_encode($this->fromFunction(fn (#[ToolParameter('desc', new JsonSchemaArray('string'))] array $x) => null))
        );
        $this->assertEquals(
            json_encode(['type' => 'object', 'properties' => ['x' => ['type' => 'array', 'items' => ['type' => 'number'], 'description' => 'desc']], 'required' => ['x']]),
            json_encode($this->fromFunction(fn (#[ToolParameter('desc', new JsonSchemaArray('float'))] array $x) => null))
        );
        $this->assertEquals(
            json_encode(['type' => 'object', 'properties' => ['x' => ['type' => 'array', 'items' => ['type' => 'number'], 'description' => 'desc']], 'required' => ['x']]),
            json_encode($this->fromFunction(fn (#[ToolParameter('desc', new JsonSchemaArray('int'))] array $x) => null))
        );
        $this->assertEquals(
            json_encode(['type' => 'object', 'properties' => ['x' => ['type' => 'array', 'items' => ['type' => 'boolean'], 'description' => 'desc']], 'required' => ['x']]),
            json_encode($this->fromFunction(fn (#[ToolParameter('desc', new JsonSchemaArray('bool'))] array $x) => null))
        );
        $this->assertEquals(
            json_encode(['type' => 'object', 'properties' => ['x' => ['type' => 'array', 'items' => ['type' => 'null'], 'description' => 'desc']], 'required' => ['x']]),
            json_encode($this->fromFunction(fn (#[ToolParameter('desc', new JsonSchemaArray('null'))] array $x) => null))
        );
    }

    public function test_that_it_handles_arrays_of_nullable_basic_types(): void
    {
        $this->assertEquals(
            json_encode(['type' => 'object', 'properties' => ['x' => ['type' => 'array', 'items' => ['type' => ['null', 'string']], 'description' => 'desc']], 'required' => ['x']]),
            json_encode($this->fromFunction(fn (#[ToolParameter('desc', new JsonSchemaArray(new JsonSchemaUnion('null', 'string')))] array $x) => null))
        );
        $this->assertEquals(
            json_encode(['type' => 'object', 'properties' => ['x' => ['type' => 'array', 'items' => ['type' => ['null', 'number']], 'description' => 'desc']], 'required' => ['x']]),
            json_encode($this->fromFunction(fn (#[ToolParameter('desc', new JsonSchemaArray(new JsonSchemaUnion('null', 'float')))] array $x) => null))
        );
        $this->assertEquals(
            json_encode(['type' => 'object', 'properties' => ['x' => ['type' => 'array', 'items' => ['type' => ['null', 'number']], 'description' => 'desc']], 'required' => ['x']]),
            json_encode($this->fromFunction(fn (#[ToolParameter('desc', new JsonSchemaArray(new JsonSchemaUnion('null', 'int')))] array $x) => null))
        );
        $this->assertEquals(
            json_encode(['type' => 'object', 'properties' => ['x' => ['type' => 'array', 'items' => ['type' => ['boolean', 'null']], 'description' => 'desc']], 'required' => ['x']]),
            json_encode($this->fromFunction(fn (#[ToolParameter('desc', new JsonSchemaArray(new JsonSchemaUnion('null', 'bool')))] array $x) => null))
        );
        $this->assertEquals(
            json_encode(['type' => 'object', 'properties' => ['x' => ['type' => 'array', 'items' => ['type' => 'null'], 'description' => 'desc']], 'required' => ['x']]),
            json_encode($this->fromFunction(fn (#[ToolParameter('desc', new JsonSchemaArray('null'))] array $x) => null))
        );
    }
}
