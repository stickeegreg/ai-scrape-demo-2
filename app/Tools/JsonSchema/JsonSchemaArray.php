<?php

namespace App\Tools\JsonSchema;

use App\Tools\JsonSchema\AbstractJsonSchemaType;

class JsonSchemaArray extends AbstractJsonSchemaType
{
    private JsonSchemaType $itemType;

    public function __construct(JsonSchemaType|string $itemType)
    {
        $this->itemType = is_string($itemType) ? JsonSchema::fromPhpType($itemType) : $itemType;
    }

    public function jsonSerialize(): mixed
    {
        return (object)array_filter([
            'type' => 'array',
            'items' => $this->itemType->jsonSerialize(),
            'description' => $this->description,
        ]);
    }
}
