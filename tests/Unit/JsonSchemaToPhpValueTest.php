<?php

namespace Tests\Unit;

use App\Tools\JsonSchema\JsonSchema;
use InvalidArgumentException;
use ReflectionParameter;
use Tests\Fixtures\SampleBackedEnum;
use Tests\Fixtures\SampleEnum;
use Tests\Fixtures\SampleObjectSimple;
use Tests\Fixtures\SampleObjectSimpleInt;
use Tests\TestCase;

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

    private function toPhpValue(ReflectionParameter $parameter, mixed $value): mixed
    {
        $jsonSchema = new JsonSchema();

        return $jsonSchema->getCastToPhp($parameter)($value);
    }

    public function test_that_it_handles_basic_variables(): void
    {
        $this->assertEquals('test', $this->toPhpValue($this->getReflectionType('string'), 'test'));
        $this->assertEquals(1, $this->toPhpValue($this->getReflectionType('int'), 1));
        $this->assertEquals(1.0, $this->toPhpValue($this->getReflectionType('float'), 1.0));
        $this->assertEquals(true, $this->toPhpValue($this->getReflectionType('bool'), true));
        $this->assertEquals(null, $this->toPhpValue($this->getReflectionType('null'), null));
    }

    public function test_that_it_handles_nullable_basic_variables(): void
    {
        $this->assertEquals('test', $this->toPhpValue($this->getReflectionType('?string'), 'test'));
        $this->assertEquals(1, $this->toPhpValue($this->getReflectionType('?int'), 1));
        $this->assertEquals(1.0, $this->toPhpValue($this->getReflectionType('?float'), 1.0));
        $this->assertEquals(true, $this->toPhpValue($this->getReflectionType('?bool'), true));
        $this->assertEquals(null, $this->toPhpValue($this->getReflectionType('?string'), null));
        $this->assertEquals(null, $this->toPhpValue($this->getReflectionType('?int'), null));
        $this->assertEquals(null, $this->toPhpValue($this->getReflectionType('?float'), null));
        $this->assertEquals(null, $this->toPhpValue($this->getReflectionType('?bool'), null));
    }

    public function test_that_it_handles_arrays_of_basic_types(): void
    {
        $this->assertEquals(['1', '2'], $this->toPhpValue($this->getReflectionType('array', 'new \\App\\Tools\\JsonSchema\\JsonSchemaArray("string")'), ['1', '2']));
        $this->assertEquals([1, 2], $this->toPhpValue($this->getReflectionType('array', 'new \\App\\Tools\\JsonSchema\\JsonSchemaArray("int")'), [1, 2]));
        $this->assertEquals([1.0, 2.0], $this->toPhpValue($this->getReflectionType('array', 'new \\App\\Tools\\JsonSchema\\JsonSchemaArray("float")'), [1.0, 2.0]));
        $this->assertEquals([true, false], $this->toPhpValue($this->getReflectionType('array', 'new \\App\\Tools\\JsonSchema\\JsonSchemaArray("bool")'), [true, false]));
        $this->assertEquals([null, null], $this->toPhpValue($this->getReflectionType('array', 'new \\App\\Tools\\JsonSchema\\JsonSchemaArray("null")'), [null, null]));
    }

    public function test_that_it_handles_arrays_of_nullable_basic_types(): void
    {
        $this->assertEquals(['1', '2', null], $this->toPhpValue($this->getReflectionType('array', 'new \\App\\Tools\\JsonSchema\\JsonSchemaArray("?string")'), ['1', '2', null]));
        $this->assertEquals([1, 2, null], $this->toPhpValue($this->getReflectionType('array', 'new \\App\\Tools\\JsonSchema\\JsonSchemaArray("?int")'), [1, 2, null]));
        $this->assertEquals([1.0, 2.0, null], $this->toPhpValue($this->getReflectionType('array', 'new \\App\\Tools\\JsonSchema\\JsonSchemaArray("?float")'), [1.0, 2.0, null]));
        $this->assertEquals([true, false, null], $this->toPhpValue($this->getReflectionType('array', 'new \\App\\Tools\\JsonSchema\\JsonSchemaArray("?bool")'), [true, false, null]));
    }

    public function test_that_it_handles_enums(): void
    {
        $this->assertEquals(SampleEnum::PEGASUS, $this->toPhpValue($this->getReflectionType(SampleEnum::class), 'PEGASUS'));
        $this->assertEquals(SampleBackedEnum::PEGASUS, $this->toPhpValue($this->getReflectionType(SampleBackedEnum::class), 'PEGASUS'));
    }

    public function test_that_it_handles_nullable_enums(): void
    {
        $this->assertEquals(SampleEnum::PEGASUS, $this->toPhpValue($this->getReflectionType('?' . SampleEnum::class), 'PEGASUS'));
        $this->assertEquals(SampleBackedEnum::PEGASUS, $this->toPhpValue($this->getReflectionType('?' . SampleBackedEnum::class), 'PEGASUS'));
        $this->assertEquals(null, $this->toPhpValue($this->getReflectionType('?' . SampleEnum::class), null));
        $this->assertEquals(null, $this->toPhpValue($this->getReflectionType('?' . SampleBackedEnum::class), null));
    }

    public function test_that_it_handles_classes(): void
    {
        $value = $this->toPhpValue($this->getReflectionType(SampleObjectSimpleInt::class), (object) ['x' => 1]);
        $this->assertInstanceOf(SampleObjectSimpleInt::class, $value);
        $this->assertEquals(1, $value->x);
    }

    public function test_that_it_handles_nullable_classes(): void
    {
        $value = $this->toPhpValue($this->getReflectionType('?' . SampleObjectSimpleInt::class), (object) ['x' => 1]);
        $this->assertInstanceOf(SampleObjectSimpleInt::class, $value);
        $this->assertEquals(1, $value->x);
    }

    public function test_that_it_handles_invalid_classes_with_wrong_type(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->toPhpValue($this->getReflectionType(SampleObjectSimpleInt::class), (object) ['x' => '1']);
    }

    public function test_that_it_handles_invalid_classes_with_missing_properties(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->toPhpValue($this->getReflectionType(SampleObjectSimpleInt::class), (object) []);
    }

    public function test_that_it_handles_invalid_classes_with_extra_properties(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->toPhpValue($this->getReflectionType(SampleObjectSimpleInt::class), (object) ['x' => 1, 'y' => 2]);
    }

    public function test_that_it_handles_unions(): void
    {
        $this->assertEquals(1, $this->toPhpValue($this->getReflectionType('int|string'), 1));
        $this->assertEquals('1', $this->toPhpValue($this->getReflectionType('int|string'), '1'));

        $value = $this->toPhpValue($this->getReflectionType(SampleObjectSimple::class . '|' . SampleObjectSimpleInt::class), (object) ['x' => 1]);
        $this->assertInstanceOf(SampleObjectSimpleInt::class, $value);
        $this->assertEquals(1, $value->x);

        $value = $this->toPhpValue($this->getReflectionType(SampleObjectSimple::class . '|' . SampleObjectSimpleInt::class), (object) []);
        $this->assertInstanceOf(SampleObjectSimple::class, $value);
    }

    public function test_that_it_handles_a_complex_type(): void
    {
        $type = $this->getReflectionType('array', 'new \\App\\Tools\\JsonSchema\\JsonSchemaArray(new \\App\\Tools\\JsonSchema\\JsonSchemaUnion(new \\App\\Tools\\JsonSchema\\JsonSchemaObject(\\Tests\\Fixtures\\SampleObjectSimple::class), new \\App\\Tools\\JsonSchema\\JsonSchemaObject(\\Tests\\Fixtures\\SampleObjectSimpleInt::class), new \\App\\Tools\\JsonSchema\\JsonSchemaArray(\\Tests\\Fixtures\\SampleObjectSimpleInt::class)))');
        $value = $this->toPhpValue($type, [(object) ['x' => 1], (object) [], (object) ['x' => 2], [(object) ['x' => 1], (object) ['x' => 2]]]);

        $this->assertIsArray($value);
        $this->assertCount(4, $value);
        $this->assertInstanceOf(SampleObjectSimpleInt::class, $value[0]);
        $this->assertEquals(1, $value[0]->x);
        $this->assertInstanceOf(SampleObjectSimple::class, $value[1]);
        $this->assertInstanceOf(SampleObjectSimpleInt::class, $value[2]);
        $this->assertEquals(2, $value[2]->x);
        $this->assertIsArray($value[3]);
        $this->assertCount(2, $value[3]);
        $this->assertInstanceOf(SampleObjectSimpleInt::class, $value[3][0]);
        $this->assertEquals(1, $value[3][0]->x);
        $this->assertInstanceOf(SampleObjectSimpleInt::class, $value[3][1]);
        $this->assertEquals(2, $value[3][1]->x);
    }
}
