<?php

namespace App\Tools\JsonSchema;

use App\Tools\JsonSchema\AbstractJsonSchemaType;
use InvalidArgumentException;

class JsonSchemaNull extends AbstractJsonSchemaType
{
    public function jsonSerialize(): mixed
    {
        return (object)array_filter([
            'type' => 'null',
            'description' => $this->description,
        ]);
    }

    public function toPhpValue(mixed $value): null
    {
        if (!is_null($value)) {
            throw new InvalidArgumentException("Value must be null");
        }

        return null;
    }
}
