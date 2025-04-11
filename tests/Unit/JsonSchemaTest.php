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
use Tests\Fixtures\SampleBackedEnum;
use Tests\Fixtures\SampleEnum;
use Tests\Fixtures\SampleObject;
use Tests\Fixtures\SampleObjectSimple;
use Tests\Fixtures\SampleObjectSimpleInt;

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
            json_encode(['type' => ['boolean', 'null']]),
            json_encode(JsonSchema::fromPhpType((new ReflectionParameter(fn (?bool $x) => null, 0))->getType()))
        );
        $this->assertEquals(
            json_encode(['type' => 'null']),
            json_encode(JsonSchema::fromPhpType((new ReflectionParameter(fn (null $x) => null, 0))->getType()))
        );
    }

    public function test_that_it_handles_nullable_basic_variables_as_strings(): void
    {
        $this->assertEquals(
            json_encode(['type' => ['null', 'string']]),
            json_encode(JsonSchema::fromPhpType('?string'))
        );
        $this->assertEquals(
            json_encode(['type' => ['null', 'number']]),
            json_encode(JsonSchema::fromPhpType('?int'))
        );
        $this->assertEquals(
            json_encode(['type' => ['null', 'number']]),
            json_encode(JsonSchema::fromPhpType('?float'))
        );
        $this->assertEquals(
            json_encode(['type' => ['boolean', 'null']]),
            json_encode(JsonSchema::fromPhpType('?bool'))
        );
    }

    public function test_that_it_requires_a_type(): void
    {
        $this->expectException(Exception::class);

        JsonSchema::fromFunction(new ReflectionFunction(new ReflectionFunction(fn ($x) => null)));
    }

    public function test_that_it_rejects_invalid_types(): void
    {
        $this->expectException(Exception::class);

        JsonSchema::fromPhpType('boolean');
    }

    public function test_that_it_requires_an_attribute_for_arrays(): void
    {
        $this->expectException(Exception::class);

        JsonSchema::fromFunction(new ReflectionFunction(new ReflectionFunction(fn (array $x) => null)));
    }

    public function test_that_it_handles_arrays_of_basic_types(): void
    {
        $this->assertEquals(
            json_encode(['type' => 'array', 'items' => ['type' => 'string']]),
            json_encode(new JsonSchemaArray('string'))
        );
        $this->assertEquals(
            json_encode(['type' => 'array', 'items' => ['type' => 'number']]),
            json_encode(new JsonSchemaArray('float'))
        );
        $this->assertEquals(
            json_encode(['type' => 'array', 'items' => ['type' => 'number']]),
            json_encode(new JsonSchemaArray('int'))
        );
        $this->assertEquals(
            json_encode(['type' => 'array', 'items' => ['type' => 'boolean']]),
            json_encode(new JsonSchemaArray('bool'))
        );
        $this->assertEquals(
            json_encode(['type' => 'array', 'items' => ['type' => 'null']]),
            json_encode(new JsonSchemaArray('null'))
        );
    }

    public function test_that_it_handles_arrays_of_nullable_basic_types(): void
    {
        $this->assertEquals(
            json_encode(['type' => 'array', 'items' => ['type' => ['null', 'string']]]),
            json_encode(new JsonSchemaArray(new JsonSchemaUnion('null', 'string')))
        );
        $this->assertEquals(
            json_encode(['type' => 'array', 'items' => ['type' => ['null', 'number']]]),
            json_encode(new JsonSchemaArray(new JsonSchemaUnion('null', 'float')))
        );
        $this->assertEquals(
            json_encode(['type' => 'array', 'items' => ['type' => ['null', 'number']]]),
            json_encode(new JsonSchemaArray(new JsonSchemaUnion('null', 'int')))
        );
        $this->assertEquals(
            json_encode(['type' => 'array', 'items' => ['type' => ['boolean', 'null']]]),
            json_encode(new JsonSchemaArray(new JsonSchemaUnion('null', 'bool')))
        );
        $this->assertEquals(
            json_encode(['type' => 'array', 'items' => ['type' => 'null']]),
            json_encode(new JsonSchemaArray('null'))
        );
    }

    public function test_that_it_handles_empty_classes(): void
    {
        $this->assertEquals(
            json_encode(['type' => 'object']),
            json_encode(JsonSchema::fromPhpType(SampleObjectSimple::class))
        );

        $this->assertEquals(
            json_encode(['type' => 'object', 'properties' => ['x' => ['type' => 'object', 'description' => 'desc']], 'required' => ['x']]),
            json_encode(JsonSchema::fromFunction(new ReflectionFunction(fn (#[ToolParameter('desc')] SampleObjectSimple $x) => null)))
        );
    }

    public function test_that_it_handles_nullable_classes(): void
    {
        $this->assertEquals(
            json_encode(['oneOf' => [['type' => 'null'], ['type' => 'object']]]),
            json_encode(JsonSchema::fromPhpType('?' . SampleObjectSimple::class))
        );

        $this->assertEquals(
            json_encode(['type' => 'object', 'properties' => ['x' => ['oneOf' => [['type' => 'null'], ['type' => 'object']], 'description' => 'desc']], 'required' => ['x']]),
            json_encode(JsonSchema::fromFunction(new ReflectionFunction(fn (#[ToolParameter('desc')] ?SampleObjectSimple $x) => null)))
        );
    }

    public function test_that_it_handles_enums(): void
    {
        $this->assertEquals(
            json_encode(['enum' => ['PEGASUS', 'UNICORN', 'EARTH_PONY']]),
            json_encode(JsonSchema::fromPhpType(SampleEnum::class))
        );

        $this->assertEquals(
            json_encode(['enum' => ['PEGASUS', 'UNICORN', 'EARTH_PONY']]),
            json_encode(JsonSchema::fromPhpType(SampleBackedEnum::class))
        );

        $this->assertEquals(
            json_encode(['type' => 'object', 'properties' => ['x' => ['enum' => ['PEGASUS', 'UNICORN', 'EARTH_PONY'], 'description' => 'desc']], 'required' => ['x']]),
            json_encode(JsonSchema::fromFunction(new ReflectionFunction(fn (#[ToolParameter('desc')] SampleEnum $x) => null)))
        );

        $this->assertEquals(
            json_encode(['type' => 'object', 'properties' => ['x' => ['enum' => ['PEGASUS', 'UNICORN', 'EARTH_PONY'], 'description' => 'desc']], 'required' => ['x']]),
            json_encode(JsonSchema::fromFunction(new ReflectionFunction(fn (#[ToolParameter('desc')] SampleBackedEnum $x) => null)))
        );
    }

    public function test_that_it_handles_nullable_enums(): void
    {
        $this->assertEquals(
            json_encode(['oneOf' => [['enum' => ['PEGASUS', 'UNICORN', 'EARTH_PONY']], ['type' => 'null']]]),
            json_encode(JsonSchema::fromPhpType('?' . SampleEnum::class))
        );

        $this->assertEquals(
            json_encode(['oneOf' => [['enum' => ['PEGASUS', 'UNICORN', 'EARTH_PONY']], ['type' => 'null']]]),
            json_encode(JsonSchema::fromPhpType('?' . SampleBackedEnum::class))
        );

        $this->assertEquals(
            json_encode(['type' => 'object', 'properties' => ['x' => ['oneOf' => [['enum' => ['PEGASUS', 'UNICORN', 'EARTH_PONY']], ['type' => 'null']], 'description' => 'desc']], 'required' => ['x']]),
            json_encode(JsonSchema::fromFunction(new ReflectionFunction(fn (#[ToolParameter('desc')] ?SampleEnum $x) => null)))
        );

        $this->assertEquals(
            json_encode(['type' => 'object', 'properties' => ['x' => ['oneOf' => [['enum' => ['PEGASUS', 'UNICORN', 'EARTH_PONY']], ['type' => 'null']], 'description' => 'desc']], 'required' => ['x']]),
            json_encode(JsonSchema::fromFunction(new ReflectionFunction(fn (#[ToolParameter('desc')] ?SampleBackedEnum $x) => null)))
        );
    }

    public function test_that_it_handles_classes(): void
    {
        $this->assertEquals(
            json_encode(['type' => 'object', 'properties' => ['x' => ['type' => 'number']]]),
            json_encode(JsonSchema::fromPhpType(SampleObjectSimpleInt::class))
        );

        $this->assertEquals(
            json_encode(['type' => 'object', 'properties' => ['x' => ['type' => 'object', 'properties' => ['x' => ['type' => 'number']], 'description' => 'desc']], 'required' => ['x']]),
            json_encode(JsonSchema::fromFunction(new ReflectionFunction(fn (#[ToolParameter('desc')] SampleObjectSimpleInt $x) => null)))
        );
    }

    public function test_that_it_handles_unions(): void
    {
        $this->assertEquals(
            json_encode(['type' => ['number', 'string']]),
            json_encode(JsonSchema::fromPhpType('string | int | float'))
        );

        $type = [
            'oneOf' => [
                ['type' => 'object', 'properties' => ['x' => ['type' => 'number']]],
                ['type' => 'object'],
            ],
        ];

        $expected = json_encode($type, JSON_PRETTY_PRINT);
        $actual = json_encode(JsonSchema::fromPhpType(SampleObjectSimple::class . '|' . SampleObjectSimpleInt::class), JSON_PRETTY_PRINT);

        $this->assertEquals($expected, $actual);

        $expected = json_encode(
            [
                'type' => 'object',
                'properties' => [
                    'x' => [
                        ...$type,
                        'description' => 'desc'
                    ]
                ],
                'required' => ['x']
            ],
            JSON_PRETTY_PRINT
        );

        $actual = json_encode(JsonSchema::fromFunction(new ReflectionFunction(fn (#[ToolParameter('desc')] SampleObjectSimple|SampleObjectSimpleInt $x) => null)), JSON_PRETTY_PRINT);

        $this->assertEquals($expected, $actual);
    }

    public function test_that_it_handles_a_complex_type(): void
    {
        $this->assertEquals(
            json_encode(['type' => ['number', 'string']]),
            json_encode(JsonSchema::fromPhpType('string | int | float'))
        );

        $type = [
            'oneOf' => [
                ['type' => 'number'],
                [
                    'type' => 'object',
                    'properties' => [
                        'name' => ['type' => ['number', 'string']],
                        'number' => ['type' => 'number'],
                        'description' => ['type' => ['null', 'string']],
                        'data' => ['type' => 'array', 'items' => ['type' => 'string']],
                        'data2' => ['type' => 'array', 'items' => ['type' => ['number', 'string']]],
                        'isActive' => ['type' => 'boolean'],
                    ],
                ],
                ['type' => 'object', 'properties' => ['x' => ['type' => 'number']]],
            ],
        ];

        $expected = json_encode($type, JSON_PRETTY_PRINT);
        $actual = json_encode(JsonSchema::fromPhpType('float|' . SampleObject::class . '|' . SampleObjectSimpleInt::class), JSON_PRETTY_PRINT);

        $this->assertEquals($expected, $actual);

        $expected = json_encode(
            [
                'type' => 'object',
                'properties' => [
                    'x' => [
                        ...$type,
                        'description' => 'desc'
                    ]
                ],
                'required' => ['x']
            ],
            JSON_PRETTY_PRINT
        );

        $actual = json_encode(JsonSchema::fromFunction(new ReflectionFunction(fn (#[ToolParameter('desc')] float|SampleObject|SampleObjectSimpleInt $x) => null)), JSON_PRETTY_PRINT);

        $this->assertEquals($expected, $actual);
    }
}
