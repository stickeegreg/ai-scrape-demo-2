<?php

namespace App\Tools\JsonSchema;

use App\Tools\JsonSchema\AbstractJsonSchemaType;
use InvalidArgumentException;

class JsonSchemaNumber extends AbstractJsonSchemaType
{
    public function __construct(
        private string $originalType
    ) {
    }

    public function jsonSerialize(): mixed
    {
        return (object)array_filter([
            'type' => 'number',
            'description' => $this->description,
        ]);
    }

    public function toPhpValue(mixed $value): int | float
    {
        if (!is_int($value) && !is_float($value)) {
            throw new InvalidArgumentException("Value must be a number");
        }

        if ($this->originalType === 'int') {
            return (int) $value;
        }

        return (float) $value;
    }
}
