<?php

namespace App\Tools\JsonSchema;

use App\Tools\JsonSchema\AbstractJsonSchemaType;

class JsonSchemaNull extends AbstractJsonSchemaType
{
    public function jsonSerialize(): mixed
    {
        return (object)array_filter([
            'type' => 'null',
            'description' => $this->description,
        ]);
    }
}
