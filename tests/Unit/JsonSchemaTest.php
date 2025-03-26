<?php

namespace Tests\Unit;

use App\Tools\Attributes\ToolParameter;
use App\Tools\JsonSchema\JsonSchema;
use App\Tools\JsonSchema\JsonSchemaArray;
use Exception;
use PHPUnit\Framework\TestCase;
use ReflectionFunction;
use ReflectionParameter;

class JsonSchemaTest extends TestCase
{
    public function test_that_it_handles_basic_variables(): void
    {
        $this->assertEquals(
            json_encode(['type' => 'string']),
            json_encode(JsonSchema::fromPhpType('string'))
        );
        $this->assertEquals(
            json_encode(['type' => 'number']),
            json_encode(JsonSchema::fromPhpType('int'))
        );
        $this->assertEquals(
            json_encode(['type' => 'number']),
            json_encode(JsonSchema::fromPhpType('float'))
        );
        $this->assertEquals(
            json_encode(['type' => 'boolean']),
            json_encode(JsonSchema::fromPhpType('bool'))
        );
        $this->assertEquals(
            json_encode(['type' => 'null']),
            json_encode(JsonSchema::fromPhpType('null'))
        );
    }

    public function test_that_it_handles_nullable_basic_variables(): void
    {
        $this->assertEquals(
            json_encode(['type' => ['null', 'string']]),
            json_encode(JsonSchema::fromPhpType((new ReflectionParameter(fn (?string $x) => null, 0))->getType()))
        );
        $this->assertEquals(
            json_encode(['type' => ['null', 'number']]),
            json_encode(JsonSchema::fromPhpType((new ReflectionParameter(fn (?int $x) => null, 0))->getType()))
        );
        $this->assertEquals(
            json_encode(['type' => ['null', 'number']]),
            json_encode(JsonSchema::fromPhpType((new ReflectionParameter(fn (?float $x) => null, 0))->getType()))
        );
        $this->assertEquals(
            json_encode(['type' => ['null', 'boolean']]),
            json_encode(JsonSchema::fromPhpType((new ReflectionParameter(fn (?bool $x) => null, 0))->getType()))
        );
        $this->assertEquals(
            json_encode(['type' => 'null']),
            json_encode(JsonSchema::fromPhpType((new ReflectionParameter(fn (null $x) => null, 0))->getType()))
        );
    }

    public function test_that_it_requires_a_type(): void
    {
        $this->expectException(Exception::class);

        JsonSchema::fromFunction(new ReflectionFunction(fn ($x) => null));
    }

    public function test_that_it_requires_an_attribute_for_arrays(): void
    {
        $this->expectException(Exception::class);

        JsonSchema::fromFunction(new ReflectionFunction(fn (array $x) => null));
    }

    public function test_that_it_handles_arrays_of_basic_types(): void
    {
        $this->assertEquals(
            json_encode(['type' => 'object', 'properties' => ['x' => ['type' => 'array', 'items' => ['type' => 'string'], 'description' => 'desc']], 'required' => ['x']]),
            json_encode(JsonSchema::fromFunction(new ReflectionFunction(fn (#[ToolParameter('desc', new JsonSchemaArray('string'))] array $x) => null)))
        );
        // $this->assertEquals(
        //     json_encode(['type' => 'array', 'items' => ['type' => 'number']]),
        //     json_encode(JsonSchema::fromFunction(new ReflectionFunction(fn (#[ToolParameter('desc', new JsonSchemaArray('int'))] array $x) => null)))
        // );
        // $this->assertEquals(
        //     json_encode(['type' => 'array', 'items' => ['type' => 'number']]),
        //     json_encode(JsonSchema::fromFunction(new ReflectionFunction(fn (#[ToolParameter('desc', new JsonSchemaArray('float'))] array $x) => null)))
        // );
        // $this->assertEquals(
        //     json_encode(['type' => 'array', 'items' => ['type' => 'boolean']]),
        //     json_encode(JsonSchema::fromFunction(new ReflectionFunction(fn (#[ToolParameter('desc', new JsonSchemaArray('bool'))] array $x) => null)))
        // );
        // $this->assertEquals(
        //     json_encode(['type' => 'array', 'items' => ['type' => 'null']]),
        //     json_encode(JsonSchema::fromFunction(new ReflectionFunction(fn (#[ToolParameter('desc', new JsonSchemaArray('null'))] array $x) => null)))
        // );
    }
}
