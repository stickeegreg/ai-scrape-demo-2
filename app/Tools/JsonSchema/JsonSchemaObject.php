<?php

namespace App\Tools\JsonSchema;

use App\Tools\Attributes\ToolProperty;
use App\Tools\JsonSchema\AbstractJsonSchemaType;
use Exception;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionProperty;

class JsonSchemaObject extends AbstractJsonSchemaType
{
    public function __construct(
        private string $className,
    ) {
        if (!class_exists($className)) {
            throw new InvalidArgumentException("Class $className does not exist");
        }
    }

    private function getValidProperties(): array
    {
        $reflection = new ReflectionClass($this->className);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);
        $validProperties = [];

        foreach ($properties as $property) {
            $propertyType = $this->getPropertyType($property);

            if ($propertyType === null) {
                continue;
            }

            $validProperties[] = $property;
        }

        return $validProperties;
    }

    private function getPropertyType(ReflectionProperty $property): ?JsonSchemaType
    {
        $toolProperty = ($property->getAttributes(ToolProperty::class)[0] ?? null)?->newInstance();

        if ($toolProperty === null) {
            return null;
        }

        $propertyName = $property->getName();

        if ($toolProperty->type !== null) {
            return $toolProperty->type;
        }

        if ($property->getType() === null) {
            throw new Exception("Property $propertyName does not have a type");
        }

        return JsonSchema::fromPhpType($property->getType());
    }

    public function jsonSerialize(): mixed
    {
        $required = [];
        $propertiesSchema = [];

        foreach ($this->getValidProperties() as $property) {
            $propertyName = $property->getName();
            $propertyType = $this->getPropertyType($property);

            $propertiesSchema[$propertyName] = $propertyType->jsonSerialize();

            // TODO fix this
            // TODO support optional properties?
            // $required[] = $propertyName;
        }

        return (object)array_filter([
            'type' => 'object',
            'properties' => $propertiesSchema,
            'required' => $required,
            'description' => $this->description,
        ]);
    }

    public function toPhpValue(mixed $value): object
    {
        if (!is_object($value)) {
            throw new InvalidArgumentException("Value must be an object");
        }

        $valueProperties = get_object_vars($value);

        $validPropertyNames = array_map(fn($property) => $property->getName(),  $this->getValidProperties());
        $givenPropertyNames = array_keys($valueProperties);

        sort($validPropertyNames);
        sort($givenPropertyNames);

        if ($givenPropertyNames !== $validPropertyNames) {
            throw new InvalidArgumentException("Value has invalid properties");
        }

        $result = new $this->className();

        foreach ($this->getValidProperties() as $property) {
            $propertyName = $property->getName();
            $result->$propertyName = $this->getPropertyType($property)->toPhpValue($valueProperties[$propertyName]);
        }

        return $result;
    }
}
