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
    public function fromPhpType(string|ReflectionType $phpType): JsonSchemaType
    {
        if (is_string($phpType)) {
            $rawPhpType = preg_replace('/^\s*\?\s*/', '', $phpType);
            $isNullable = $rawPhpType !== $phpType;
            $types = [];

            if ($isNullable) {
                $types[] = new JsonSchemaNull();
            }

            foreach (explode('|', $rawPhpType) as $type) {
                $type = mb_trim($type);

                $types[] = match ($type) {
                    // 'array' requires extra data so cannot be automatically converted, use e.g. #[ToolParameter('description', new JsonSchemaArray('int'))] instead
                    'bool' => new JsonSchemaBoolean(),
                    'float', 'int' => new JsonSchemaNumber($type),
                    'null' => new JsonSchemaNull(),
                    // 'object' => new JsonSchemaObject(),
                    'string' => new JsonSchemaString(),
                    default => class_exists($type) ? $this->fromClassName($type) : throw new InvalidArgumentException("Unsupported PHP type: $phpType"),
                };
            }

            return count($types) === 1 ? $types[0] : new JsonSchemaUnion(...$types);
        }

        if ($phpType->allowsNull()) {
            if ($phpType->getName() === 'null') {
                return new JsonSchemaNull();
            }

            return new JsonSchemaUnion(new JsonSchemaNull(), $this->fromPhpType($phpType->getName()));
        }

        if ($phpType instanceof ReflectionUnionType) {
            $types = array_map(fn ($type) => $this->fromPhpType($type), $phpType->getTypes());

            return new JsonSchemaUnion(...$types);
        }

        if ($phpType instanceof ReflectionNamedType) {
            // TODO probably wrong? nullable?
            // dump($phpType);
            return $this->fromPhpType($phpType->getName());
        }

        throw new InvalidArgumentException("Unsupported PHP type");
    }

    private function fromClassName(string $className): JsonSchemaType
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

    private function getToolParameter(ReflectionParameter $parameter): ToolParameter
    {
        if ($parameter->isVariadic()) {
            throw new Exception('Variadic parameters are not supported: $' . $parameter->getName());
        }

        $parameterAttributes = $parameter->getAttributes(ToolParameter::class);

        if ($parameterAttributes === []) {
            throw new Exception('Missing ToolParameter attribute for: $' . $parameter->getName());
        }

        return $parameterAttributes[0]->newInstance();
    }

    public function fromMethod(ReflectionMethod $method): object
    {
        return $this->fromMethodOrFunction($method);
    }

    public function fromFunction(ReflectionFunction $function): object
    {
        return $this->fromMethodOrFunction($function);
    }

    private function fromMethodOrFunction(ReflectionFunction | ReflectionMethod $function): object
    {
        $parameters = $function->getParameters();

        $properties = [];
        $required = [];

        foreach ($parameters as $parameter) {
            $toolParameter = $this->getToolParameter($parameter);
            $type = $toolParameter->type ?? $this->fromPhpType($parameter->getType());
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

    public function getCastToPhp(ReflectionParameter $reflectionParameter): Closure
    {
        return fn (mixed $value): mixed => $this->toPhpValue($reflectionParameter, $value);
    }

    private function toPhpValue(ReflectionParameter $parameter, mixed $value): mixed
    {
        $toolParameter = $this->getToolParameter($parameter);
        $type = $toolParameter->type ?? $this->fromPhpType($parameter->getType());

        return $type->toPhpValue($value);
    }
}
