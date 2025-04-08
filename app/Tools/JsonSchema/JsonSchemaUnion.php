<?php

namespace App\Tools\JsonSchema;

use App\Tools\JsonSchema\AbstractJsonSchemaType;
use InvalidArgumentException;

class JsonSchemaUnion extends AbstractJsonSchemaType
{
    private array $types;

    public function __construct(
        JsonSchemaType|string ...$types
    ) {
        if ($types === []) {
            throw new InvalidArgumentException("Union must have at least one type");
        }

        $this->types = array_map(fn($type) => is_string($type) ? JsonSchema::fromPhpType($type) : $type, $types);
    }

    public function jsonSerialize(): mixed
    {
        $useOneOf = array_filter($this->types, fn ($type) => in_array($type->jsonSerialize()->type ?? null, ['object', 'array', null])) !== [];

        if ($useOneOf) {
            $types = array_unique(array_map(fn ($type) => $type->jsonSerialize(), $this->types), SORT_REGULAR);
            usort($types, fn ($a, $b) => json_encode($a) <=> json_encode($b));

            return (object)array_filter([
                'oneOf' => $types,
                'description' => $this->description,
            ]);
        }

        $types = array_unique(array_reduce($this->types, fn ($carry, $type) => [...$carry, $type->jsonSerialize()->type], []));
        sort($types);

        return (object)array_filter([
            'type' => $types,
            'description' => $this->description,
        ]);
    }

    public function toPhpValue(mixed $value): mixed
    {
        foreach ($this->types as $type) {
            try {
                return $type->toPhpValue($value);
            } catch (InvalidArgumentException) {
                // Continue to the next type
            }
        }

        throw new InvalidArgumentException("Value does not match any of the union types");
    }
}
