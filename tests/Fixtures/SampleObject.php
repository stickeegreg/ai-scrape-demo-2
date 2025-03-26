<?php

namespace Tests\Fixtures;

use App\Tools\Attributes\ToolProperty;
use App\Tools\JsonSchema\JsonSchemaArray;
use App\Tools\JsonSchema\JsonSchemaNumber;
use App\Tools\JsonSchema\JsonSchemaString;
use App\Tools\JsonSchema\JsonSchemaUnion;

class SampleObject
{
    #[ToolProperty('The name of the object')]
    public string|int $name = 'TestObject';

    #[ToolProperty('The number of the object')]
    public int $number = 123;

    #[ToolProperty('The description of the object')]
    public ?string $description = null;

    #[ToolProperty('An array of strings', new JsonSchemaArray(new JsonSchemaString()))]
    public array $data = [];

    #[ToolProperty('An array of strings', new JsonSchemaArray(new JsonSchemaUnion([new JsonSchemaString(), new JsonSchemaNumber())), 'An array of strings'))]
    public array $data2 = [];

    #[ToolProperty('The status of the object')]
    public bool $isActive = true;
}
