<?php

namespace App\Tools\JsonSchema;

use App\Tools\JsonSchema\AbstractJsonSchemaType;
use InvalidArgumentException;

class JsonSchemaString extends AbstractJsonSchemaType
{
    public function jsonSerialize(): mixed
    {
        return (object)array_filter([
            'type' => 'string',
            'description' => $this->description,
        ]);
    }

    public function toPhpValue(mixed $value): string
    {
        if (!is_string($value)) {
            throw new InvalidArgumentException("Value must be a string");
        }

        return (string) $value;
    }
}
