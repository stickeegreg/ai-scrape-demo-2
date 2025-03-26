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

    public function jsonSerialize(): mixed
    {
        $reflection = new ReflectionClass($this->className);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);

        $required = [];
        $propertiesSchema = [];

        foreach ($properties as $property) {
            $toolProperty = ($property->getAttributes(ToolProperty::class)[0] ?? null)?->newInstance();

            if ($toolProperty === null) {
                continue;
            }

            $propertyName = $property->getName();
            $propertyType = null;
            $isRequired = false;

            if ($toolProperty->type !== null) {
                $propertyType = $toolProperty->type;
            } else {
                if ($property->getType() === null) {
                    throw new Exception("Property $propertyName does not have a type");
                }

                $propertyType = JsonSchema::fromPhpType($property->getType());
            }

            $propertiesSchema[$propertyName] = $propertyType->jsonSerialize();

            if ($required) {
                $required[] = $propertyName;
            }
        }

        return (object)array_filter([
            'type' => 'object',
            'properties' => $propertiesSchema,
            'required' => $required,
            'description' => $this->description,
        ]);
    }
}
