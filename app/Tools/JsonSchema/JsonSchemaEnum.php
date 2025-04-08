<?php

namespace App\Tools\JsonSchema;

use App\Tools\JsonSchema\AbstractJsonSchemaType;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionEnum;

class JsonSchemaEnum extends AbstractJsonSchemaType
{
    public function __construct(
        private string $className,
    ) {
        if (!class_exists($className)) {
            throw new InvalidArgumentException("Class $className does not exist");
        }

        $reflection = new ReflectionClass($className);

        if (!$reflection->isEnum()) {
            throw new InvalidArgumentException("$className is not an enum");
        }
    }

    public function jsonSerialize(): mixed
    {
        $reflection = new ReflectionEnum($this->className);

        $values = array_map(fn($case) => $case->name, $reflection->getCases());

        return (object)array_filter([
            'enum' => $values,
            'description' => $this->description,
        ]);
    }

    public function toPhpValue(mixed $value): mixed
    {
        if (!is_string($value)) {
            throw new InvalidArgumentException("Value must be a string");
        }

        if (!enum_exists($this->className)) {
            throw new InvalidArgumentException("Enum $this->className does not exist");
        }

        if (!in_array($value, array_map(fn($case) => $case->name, $this->className::cases()))) {
            throw new InvalidArgumentException("Value $value is not a valid case of enum $this->className");
        }

        return $this->className::{$value};
    }
}
