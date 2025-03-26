<?php

namespace App\Tools\JsonSchema;

use App\Tools\JsonSchema\AbstractJsonSchemaType;

class JsonSchemaBoolean extends AbstractJsonSchemaType
{
    public function jsonSerialize(): mixed
    {
        return (object)array_filter([
            'type' => 'boolean',
            'description' => $this->description,
        ]);
    }
}
