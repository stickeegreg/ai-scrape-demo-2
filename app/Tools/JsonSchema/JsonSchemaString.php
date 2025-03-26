<?php

namespace App\Tools\JsonSchema;

use App\Tools\JsonSchema\AbstractJsonSchemaType;

class JsonSchemaString extends AbstractJsonSchemaType
{
    public function jsonSerialize(): mixed
    {
        return (object)array_filter([
            'type' => 'string',
            'description' => $this->description,
        ]);
    }
}
