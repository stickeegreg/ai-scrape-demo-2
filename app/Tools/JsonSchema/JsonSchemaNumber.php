<?php

namespace App\Tools\JsonSchema;

use App\Tools\JsonSchema\AbstractJsonSchemaType;

class JsonSchemaNumber extends AbstractJsonSchemaType
{
    public function jsonSerialize(): mixed
    {
        return (object)array_filter([
            'type' => 'number',
            'description' => $this->description,
        ]);
    }
}
