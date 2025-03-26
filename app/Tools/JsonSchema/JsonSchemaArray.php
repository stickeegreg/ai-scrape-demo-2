<?php

namespace App\Tools\JsonSchema;

use App\Tools\JsonSchema\AbstractJsonSchemaType;

class JsonSchemaArray extends AbstractJsonSchemaType
{
    public function __construct(
        private JsonSchemaType|string $itemType
    ) {
    }

    public function jsonSerialize(): mixed
    {
        return (object)array_filter([
            'type' => 'array',
            'items' => is_string($this->itemType) ? JsonSchema::fromPhpType($this->itemType)->jsonSerialize() : $this->itemType->jsonSerialize(),
            'description' => $this->description,
        ]);
    }
}
