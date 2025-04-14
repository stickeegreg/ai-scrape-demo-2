<?php

namespace App\Tools\JsonSchema;

use App\Tools\JsonSchema\AbstractJsonSchemaType;
use App\Tools\JsonSchema\Facades\JsonSchema;
use InvalidArgumentException;

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

    public function toPhpValue(mixed $value): array
    {
        if (!is_array($value)) {
            throw new InvalidArgumentException("Value must be an array");
        }

        $value = array_map(
            fn($item) => $this->itemType->toPhpValue($item),
            $value
        );

        return $value;
    }
}
