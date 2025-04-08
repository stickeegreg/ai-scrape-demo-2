<?php

namespace App\Tools\JsonSchema;

use App\Tools\JsonSchema\AbstractJsonSchemaType;
use InvalidArgumentException;

class JsonSchemaBoolean extends AbstractJsonSchemaType
{
    public function jsonSerialize(): mixed
    {
        return (object)array_filter([
            'type' => 'boolean',
            'description' => $this->description,
        ]);
    }

    public function toPhpValue(mixed $value): bool
    {
        if (!is_bool($value)) {
            throw new InvalidArgumentException("Value must be a boolean");
        }

        return (bool) $value;
    }
}
