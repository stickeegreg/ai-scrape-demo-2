<?php

namespace Tests\Unit;

use App\Tools\Attributes\ToolParameter;
use App\Tools\JsonSchema\JsonSchema;
use App\Tools\JsonSchema\JsonSchemaArray;
use App\Tools\JsonSchema\JsonSchemaUnion;
use Exception;
use PHPUnit\Framework\TestCase;
use ReflectionParameter;
use ReflectionType;
use Tests\Fixtures\SampleBackedEnum;
use Tests\Fixtures\SampleEnum;
use Tests\Fixtures\SampleObject;
use Tests\Fixtures\SampleObjectSimple;
use Tests\Fixtures\SampleObjectSimpleInt;

class JsonSchemaToPhpValueTest extends TestCase
{
    private function getReflectionType(string $type, string $jsonSchemaType = ''): ReflectionParameter
    {
        $closureString = "return fn ("
        . "#[\\App\\Tools\\Attributes\\ToolParameter('x'"
        . ($jsonSchemaType ? ", $jsonSchemaType" : '')
        . ")] $type \$x) => null;";

        return(new ReflectionParameter(eval($closureString), 0));
    }

    public function test_that_it_handles_basic_variables(): void
    {
        $this->assertEquals('test', JsonSchema::toPhpValue($this->getReflectionType('string'), 'test'));
        $this->assertEquals(1, JsonSchema::toPhpValue($this->getReflectionType('int'), 1));
        $this->assertEquals(1.0, JsonSchema::toPhpValue($this->getReflectionType('float'), 1.0));
        $this->assertEquals(true, JsonSchema::toPhpValue($this->getReflectionType('bool'), true));
        $this->assertEquals(null, JsonSchema::toPhpValue($this->getReflectionType('null'), null));
    }

    public function test_that_it_handles_nullable_basic_variables(): void
    {
        $this->assertEquals('test', JsonSchema::toPhpValue($this->getReflectionType('?string'), 'test'));
        $this->assertEquals(1, JsonSchema::toPhpValue($this->getReflectionType('?int'), 1));
        $this->assertEquals(1.0, JsonSchema::toPhpValue($this->getReflectionType('?float'), 1.0));
        $this->assertEquals(true, JsonSchema::toPhpValue($this->getReflectionType('?bool'), true));
        $this->assertEquals(null, JsonSchema::toPhpValue($this->getReflectionType('?string'), null));
        $this->assertEquals(null, JsonSchema::toPhpValue($this->getReflectionType('?int'), null));
        $this->assertEquals(null, JsonSchema::toPhpValue($this->getReflectionType('?float'), null));
        $this->assertEquals(null, JsonSchema::toPhpValue($this->getReflectionType('?bool'), null));
    }

    public function test_that_it_handles_arrays_of_basic_types(): void
    {
        $this->assertEquals(['1', '2'], JsonSchema::toPhpValue($this->getReflectionType('array', 'new \\App\\Tools\\JsonSchema\\JsonSchemaArray("string")'), ['1', '2']));
        $this->assertEquals([1, 2], JsonSchema::toPhpValue($this->getReflectionType('array', 'new \\App\\Tools\\JsonSchema\\JsonSchemaArray("int")'), [1, 2]));
        $this->assertEquals([1.0, 2.0], JsonSchema::toPhpValue($this->getReflectionType('array', 'new \\App\\Tools\\JsonSchema\\JsonSchemaArray("float")'), [1.0, 2.0]));
        $this->assertEquals([true, false], JsonSchema::toPhpValue($this->getReflectionType('array', 'new \\App\\Tools\\JsonSchema\\JsonSchemaArray("bool")'), [true, false]));
        $this->assertEquals([null, null], JsonSchema::toPhpValue($this->getReflectionType('array', 'new \\App\\Tools\\JsonSchema\\JsonSchemaArray("null")'), [null, null]));
    }

    public function test_that_it_handles_arrays_of_nullable_basic_types(): void
    {
        $this->assertEquals(['1', '2', null], JsonSchema::toPhpValue($this->getReflectionType('array', 'new \\App\\Tools\\JsonSchema\\JsonSchemaArray("?string")'), ['1', '2', null]));
        $this->assertEquals([1, 2, null], JsonSchema::toPhpValue($this->getReflectionType('array', 'new \\App\\Tools\\JsonSchema\\JsonSchemaArray("?int")'), [1, 2, null]));
        $this->assertEquals([1.0, 2.0, null], JsonSchema::toPhpValue($this->getReflectionType('array', 'new \\App\\Tools\\JsonSchema\\JsonSchemaArray("?float")'), [1.0, 2.0, null]));
        $this->assertEquals([true, false, null], JsonSchema::toPhpValue($this->getReflectionType('array', 'new \\App\\Tools\\JsonSchema\\JsonSchemaArray("?bool")'), [true, false, null]));
    }

    // public function test_that_it_handles_empty_classes(): void
    // {
    //     $this->assertEquals(
    //         json_encode(['type' => 'object']),
    //         json_encode(JsonSchema::fromPhpType(SampleObjectSimple::class))
    //     );

    //     $this->assertEquals(
    //         json_encode(['type' => 'object', 'properties' => ['x' => ['type' => 'object', 'description' => 'desc']], 'required' => ['x']]),
    //         json_encode(JsonSchema::fromFunction(fn (#[ToolParameter('desc')] SampleObjectSimple $x) => null))
    //     );
    // }

    // public function test_that_it_handles_nullable_classes(): void
    // {
    //     $this->assertEquals(
    //         json_encode(['oneOf' => [['type' => 'null'], ['type' => 'object']]]),
    //         json_encode(JsonSchema::fromPhpType('?' . SampleObjectSimple::class))
    //     );

    //     $this->assertEquals(
    //         json_encode(['type' => 'object', 'properties' => ['x' => ['oneOf' => [['type' => 'null'], ['type' => 'object']], 'description' => 'desc']], 'required' => ['x']]),
    //         json_encode(JsonSchema::fromFunction(fn (#[ToolParameter('desc')] ?SampleObjectSimple $x) => null))
    //     );
    // }

    public function test_that_it_handles_enums(): void
    {
        $this->assertEquals(SampleEnum::PEGASUS, JsonSchema::toPhpValue($this->getReflectionType(SampleEnum::class), 'PEGASUS'));
        $this->assertEquals(SampleBackedEnum::PEGASUS, JsonSchema::toPhpValue($this->getReflectionType(SampleBackedEnum::class), 'PEGASUS'));
    }

    public function test_that_it_handles_nullable_enums(): void
    {
        $this->assertEquals(SampleEnum::PEGASUS, JsonSchema::toPhpValue($this->getReflectionType('?' . SampleEnum::class), 'PEGASUS'));
        $this->assertEquals(SampleBackedEnum::PEGASUS, JsonSchema::toPhpValue($this->getReflectionType('?' . SampleBackedEnum::class), 'PEGASUS'));
        $this->assertEquals(null, JsonSchema::toPhpValue($this->getReflectionType('?' . SampleEnum::class), null));
        $this->assertEquals(null, JsonSchema::toPhpValue($this->getReflectionType('?' . SampleBackedEnum::class), null));
    }

    // public function test_that_it_handles_classes(): void
    // {
    //     $this->assertEquals(
    //         json_encode(['type' => 'object', 'properties' => ['x' => ['type' => 'number']]]),
    //         json_encode(JsonSchema::fromPhpType(SampleObjectSimpleInt::class))
    //     );

    //     $this->assertEquals(
    //         json_encode(['type' => 'object', 'properties' => ['x' => ['type' => 'object', 'properties' => ['x' => ['type' => 'number']], 'description' => 'desc']], 'required' => ['x']]),
    //         json_encode(JsonSchema::fromFunction(fn (#[ToolParameter('desc')] SampleObjectSimpleInt $x) => null))
    //     );
    // }

    // public function test_that_it_handles_unions(): void
    // {
    //     $this->assertEquals(
    //         json_encode(['type' => ['number', 'string']]),
    //         json_encode(JsonSchema::fromPhpType('string | int | float'))
    //     );

    //     $type = [
    //         'oneOf' => [
    //             ['type' => 'object', 'properties' => ['x' => ['type' => 'number']]],
    //             ['type' => 'object'],
    //         ],
    //     ];

    //     $expected = json_encode($type, JSON_PRETTY_PRINT);
    //     $actual = json_encode(JsonSchema::fromPhpType(SampleObjectSimple::class . '|' . SampleObjectSimpleInt::class), JSON_PRETTY_PRINT);

    //     $this->assertEquals($expected, $actual);

    //     $expected = json_encode(
    //         [
    //             'type' => 'object',
    //             'properties' => [
    //                 'x' => [
    //                     ...$type,
    //                     'description' => 'desc'
    //                 ]
    //             ],
    //             'required' => ['x']
    //         ],
    //         JSON_PRETTY_PRINT
    //     );

    //     $actual = json_encode(JsonSchema::fromFunction(fn (#[ToolParameter('desc')] SampleObjectSimple|SampleObjectSimpleInt $x) => null), JSON_PRETTY_PRINT);

    //     $this->assertEquals($expected, $actual);
    // }

    // public function test_that_it_handles_a_complex_type(): void
    // {
    //     $this->assertEquals(
    //         json_encode(['type' => ['number', 'string']]),
    //         json_encode(JsonSchema::fromPhpType('string | int | float'))
    //     );

    //     $type = [
    //         'oneOf' => [
    //             ['type' => 'number'],
    //             [
    //                 'type' => 'object',
    //                 'properties' => [
    //                     'name' => ['type' => ['number', 'string']],
    //                     'number' => ['type' => 'number'],
    //                     'description' => ['type' => ['null', 'string']],
    //                     'data' => ['type' => 'array', 'items' => ['type' => 'string']],
    //                     'data2' => ['type' => 'array', 'items' => ['type' => ['number', 'string']]],
    //                     'isActive' => ['type' => 'boolean'],
    //                 ],
    //             ],
    //             ['type' => 'object', 'properties' => ['x' => ['type' => 'number']]],
    //         ],
    //     ];

    //     $expected = json_encode($type, JSON_PRETTY_PRINT);
    //     $actual = json_encode(JsonSchema::fromPhpType('float|' . SampleObject::class . '|' . SampleObjectSimpleInt::class), JSON_PRETTY_PRINT);

    //     $this->assertEquals($expected, $actual);

    //     $expected = json_encode(
    //         [
    //             'type' => 'object',
    //             'properties' => [
    //                 'x' => [
    //                     ...$type,
    //                     'description' => 'desc'
    //                 ]
    //             ],
    //             'required' => ['x']
    //         ],
    //         JSON_PRETTY_PRINT
    //     );

    //     $actual = json_encode(JsonSchema::fromFunction(fn (#[ToolParameter('desc')] float|SampleObject|SampleObjectSimpleInt $x) => null), JSON_PRETTY_PRINT);

    //     $this->assertEquals($expected, $actual);
    // }
}
