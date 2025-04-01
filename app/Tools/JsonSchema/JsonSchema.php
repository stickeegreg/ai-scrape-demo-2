<?php

namespace App\Tools\JsonSchema;

use App\Tools\Attributes\ToolParameter;
use Closure;
use Exception;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;
use ReflectionUnionType;

class JsonSchema
{
    // TODO make not static
    public static function fromPhpType(string|ReflectionType $phpType): JsonSchemaType
    {
        if (is_string($phpType)) {
            $rawPhpType = preg_replace('/^\s*\?\s*/', '', $phpType);
            $isNullable = $rawPhpType !== $phpType;
            $types = [];

            if ($isNullable) {
                $types[] = new JsonSchemaNull();
            }

            foreach (explode('|', $rawPhpType) as $type) {
                $types[] = match (mb_trim($type)) {
                    // 'array' => new JsonSchemaArray(),
                    'bool' => new JsonSchemaBoolean(),
                    'float', 'int' => new JsonSchemaNumber(),
                    'null' => new JsonSchemaNull(),
                    // 'object' => new JsonSchemaObject(),
                    'string' => new JsonSchemaString(),
                    default => class_exists($type) ? self::fromClassName($type) : throw new InvalidArgumentException("Unsupported PHP type: $phpType"),
                };
            }

            return count($types) === 1 ? $types[0] : new JsonSchemaUnion(...$types);
        }

        if ($phpType->allowsNull()) {
            if ($phpType->getName() === 'null') {
                return new JsonSchemaNull();
            }

            return new JsonSchemaUnion(new JsonSchemaNull(), self::fromPhpType($phpType->getName()));
        }

        if ($phpType instanceof ReflectionUnionType) {
            $types = array_map(fn ($type) => self::fromPhpType($type), $phpType->getTypes());

            return new JsonSchemaUnion(...$types);
        }

        if ($phpType instanceof ReflectionNamedType) {
            // TODO probably wrong? nullable?
            // dump($phpType);
            return self::fromPhpType($phpType->getName());
        }

        throw new InvalidArgumentException("Unsupported PHP type");
    }

    private static function fromClassName(string $className): JsonSchemaType
    {
        if (!class_exists($className)) {
            throw new InvalidArgumentException("Class $className does not exist");
        }

        $reflection = new ReflectionClass($className);

        if ($reflection->isEnum()) {
            return new JsonSchemaEnum($className);
        }

        return new JsonSchemaObject($className);
    }

    public static function fromMethod(ReflectionMethod $method): object
    {
        $parameters = $method->getParameters();

        $properties = [];
        $required = [];

        foreach ($parameters as $parameter) {
            if ($parameter->isVariadic()) {
                throw new Exception("Variadic parameters are not supported: " . $method->getDeclaringClass()->getName() . '::' . $method->getName() . '() parameter $' . $parameter->getName());
            }

            $parameterAttributes = $parameter->getAttributes(ToolParameter::class);

            if ($parameterAttributes === []) {
                throw new Exception("Missing ToolParameter attribute for: " . $method->getDeclaringClass()->getName() . '::' . $method->getName() . '() parameter $' . $parameter->getName());
            }

            $toolParameter = $parameterAttributes[0]->newInstance();
            $type = $toolParameter->type ?? JsonSchema::fromPhpType($parameter->getType());
            $type->setDescription($toolParameter->description);

            $properties[$parameter->getName()] = $type->jsonSerialize();

            if (!$parameter->isOptional()) {
                $required[] = $parameter->getName();
            }
        }

        $inputSchema = (object) [
            "type" => "object",
            "properties" => (object) $properties,
            "required" => $required,
        ];

        return $inputSchema;
    }

    public static function fromFunction(Closure|string $function): object
    {
        $reflectionFunction = new ReflectionFunction($function);
        $parameters = $reflectionFunction->getParameters();

        $properties = [];
        $required = [];

        foreach ($parameters as $parameter) {
            if ($parameter->isVariadic()) {
                throw new Exception("Variadic parameters are not supported: " . $reflectionFunction->getName() . '() parameter $' . $parameter->getName());
            }

            $parameterAttributes = $parameter->getAttributes(ToolParameter::class);

            if ($parameterAttributes === []) {
                throw new Exception("Missing ToolParameter attribute for: " . $reflectionFunction->getName() . '() parameter $' . $parameter->getName());
            }

            $toolParameter = $parameterAttributes[0]->newInstance();
            $type = $toolParameter->type ?? JsonSchema::fromPhpType($parameter->getType());
            $type->setDescription($toolParameter->description);

            $properties[$parameter->getName()] = $type->jsonSerialize();

            if (!$parameter->isOptional()) {
                $required[] = $parameter->getName();
            }
        }

        $inputSchema = (object) [
            "type" => "object",
            "properties" => (object) $properties,
            "required" => $required,
        ];

        return $inputSchema;
    }
}
