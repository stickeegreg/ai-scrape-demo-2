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

        $values = $reflection->isBacked()
            ? array_map(fn($case) => $case->getBackingValue(), $reflection->getCases())
            : array_map(fn($case) => $case->name, $reflection->getCases());
        $values = array_unique($values);
        $values = array_values($values);

        return (object)array_filter([
            'enum' => $values,
            'description' => $this->description,
        ]);
    }
}
