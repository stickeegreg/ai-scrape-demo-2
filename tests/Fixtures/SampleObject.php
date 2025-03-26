<?php

namespace Tests\Fixtures;

use App\Tools\Attributes\ToolProperty;
use App\Tools\JsonSchema\JsonSchemaArray;

class SampleObject
{
    #[ToolProperty('The name of the object')]
    public string|int $name = 'TestObject';

    #[ToolProperty('The number of the object')]
    public int $number = 123;

    #[ToolProperty('The description of the object')]
    public ?string $description = null;

    #[ToolProperty('An array of strings', new JsonSchemaArray('string'))]
    public array $data = [];

    #[ToolProperty('An array of strings or numbers', new JsonSchemaArray('string|int'))]
    public array $data2 = [];

    #[ToolProperty('The status of the object')]
    public bool $isActive = true;
}
