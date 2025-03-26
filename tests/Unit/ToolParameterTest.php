<?php

namespace Tests\Unit;

use App\Tools\Attributes\ToolParameter;
use App\Tools\JsonSchema\JsonSchema;
use App\Tools\JsonSchema\JsonSchemaArray;
use App\Tools\JsonSchema\JsonSchemaUnion;
use Exception;
use PHPUnit\Framework\TestCase;
use ReflectionFunction;
use ReflectionParameter;

class ToolParameterTest extends TestCase
{
    public function test_that_it_handles_arrays_of_basic_types(): void
    {
        $this->assertEquals(
            json_encode(['type' => 'object', 'properties' => ['x' => ['type' => 'array', 'items' => ['type' => 'string'], 'description' => 'desc']], 'required' => ['x']]),
            json_encode(JsonSchema::fromFunction(new ReflectionFunction(fn (#[ToolParameter('desc', new JsonSchemaArray('string'))] array $x) => null)))
        );
        $this->assertEquals(
            json_encode(['type' => 'object', 'properties' => ['x' => ['type' => 'array', 'items' => ['type' => 'number'], 'description' => 'desc']], 'required' => ['x']]),
            json_encode(JsonSchema::fromFunction(new ReflectionFunction(fn (#[ToolParameter('desc', new JsonSchemaArray('float'))] array $x) => null)))
        );
        $this->assertEquals(
            json_encode(['type' => 'object', 'properties' => ['x' => ['type' => 'array', 'items' => ['type' => 'number'], 'description' => 'desc']], 'required' => ['x']]),
            json_encode(JsonSchema::fromFunction(new ReflectionFunction(fn (#[ToolParameter('desc', new JsonSchemaArray('int'))] array $x) => null)))
        );
        $this->assertEquals(
            json_encode(['type' => 'object', 'properties' => ['x' => ['type' => 'array', 'items' => ['type' => 'boolean'], 'description' => 'desc']], 'required' => ['x']]),
            json_encode(JsonSchema::fromFunction(new ReflectionFunction(fn (#[ToolParameter('desc', new JsonSchemaArray('bool'))] array $x) => null)))
        );
        $this->assertEquals(
            json_encode(['type' => 'object', 'properties' => ['x' => ['type' => 'array', 'items' => ['type' => 'null'], 'description' => 'desc']], 'required' => ['x']]),
            json_encode(JsonSchema::fromFunction(new ReflectionFunction(fn (#[ToolParameter('desc', new JsonSchemaArray('null'))] array $x) => null)))
        );
    }

    public function test_that_it_handles_arrays_of_nullable_basic_types(): void
    {
        $this->assertEquals(
            json_encode(['type' => 'object', 'properties' => ['x' => ['type' => 'array', 'items' => ['type' => ['null', 'string']], 'description' => 'desc']], 'required' => ['x']]),
            json_encode(JsonSchema::fromFunction(new ReflectionFunction(fn (#[ToolParameter('desc', new JsonSchemaArray(new JsonSchemaUnion('null', 'string')))] array $x) => null)))
        );
        $this->assertEquals(
            json_encode(['type' => 'object', 'properties' => ['x' => ['type' => 'array', 'items' => ['type' => ['null', 'number']], 'description' => 'desc']], 'required' => ['x']]),
            json_encode(JsonSchema::fromFunction(new ReflectionFunction(fn (#[ToolParameter('desc', new JsonSchemaArray(new JsonSchemaUnion('null', 'float')))] array $x) => null)))
        );
        $this->assertEquals(
            json_encode(['type' => 'object', 'properties' => ['x' => ['type' => 'array', 'items' => ['type' => ['null', 'number']], 'description' => 'desc']], 'required' => ['x']]),
            json_encode(JsonSchema::fromFunction(new ReflectionFunction(fn (#[ToolParameter('desc', new JsonSchemaArray(new JsonSchemaUnion('null', 'int')))] array $x) => null)))
        );
        $this->assertEquals(
            json_encode(['type' => 'object', 'properties' => ['x' => ['type' => 'array', 'items' => ['type' => ['null', 'boolean']], 'description' => 'desc']], 'required' => ['x']]),
            json_encode(JsonSchema::fromFunction(new ReflectionFunction(fn (#[ToolParameter('desc', new JsonSchemaArray(new JsonSchemaUnion('null', 'bool')))] array $x) => null)))
        );
        $this->assertEquals(
            json_encode(['type' => 'object', 'properties' => ['x' => ['type' => 'array', 'items' => ['type' => 'null'], 'description' => 'desc']], 'required' => ['x']]),
            json_encode(JsonSchema::fromFunction(new ReflectionFunction(fn (#[ToolParameter('desc', new JsonSchemaArray('null'))] array $x) => null)))
        );
    }
}
