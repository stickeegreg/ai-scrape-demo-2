<?php

namespace App\Tools\JsonSchema;

use App\Tools\JsonSchema\AbstractJsonSchemaType;
use InvalidArgumentException;

class JsonSchemaUnion extends AbstractJsonSchemaType
{
    private array $types;

    public function __construct(
        JsonSchemaType ...$types
    ) {
        if ($types === []) {
            throw new InvalidArgumentException("Union must have at least one type");
        }

        $this->types = $types;
    }

    public function jsonSerialize(): mixed
    {
        // TODO probably wrong? should merge?
        return (object)array_filter([
            'type' => array_reduce($this->types, fn ($carry, $type) => [...$carry, $type->jsonSerialize()->type], []),
            'description' => $this->description,
        ]);
    }
}
