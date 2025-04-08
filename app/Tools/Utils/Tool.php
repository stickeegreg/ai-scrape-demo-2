<?php

namespace App\Tools\Utils;

use App\Tools\Attributes\ToolMethod;
use App\Tools\JsonSchema\JsonSchema;
use App\Tools\Utils\ToolInterface;
use App\Tools\Utils\ToolResult;
use Closure;
use InvalidArgumentException;
use ReflectionMethod;
use ReflectionParameter;

class Tool implements ToolInterface
{
    public static function fromMethod(
        object $instance,
        ReflectionMethod $method,
    ): self {
        $toolMethod = ($method->getAttributes(ToolMethod::class)[0] ?? null)?->newInstance();

        if ($toolMethod === null) {
            throw new InvalidArgumentException('ToolMethod attribute is required.');
        }

        $casts = array_map(
            fn (ReflectionParameter $parameter): Closure => JsonSchema::getCastToPhp($parameter),
            $method->getParameters(),
        );

        $inputSchema = (object) [
            'name' => $toolMethod->name ?? $method->getName(),
            'description' => $toolMethod->description,
            'input_schema' => JsonSchema::fromMethod($method),
        ];
        $handler = $method->getClosure($instance);

        return new self($toolMethod->name ?? $method->getName(), $inputSchema, $handler, $casts);
    }

    public function __construct(
        private readonly string $name,
        private readonly object $inputSchema,
        private readonly Closure $handler,
        private readonly ?array $casts = null,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getInputSchema(): object
    {
        return $this->inputSchema;
    }

    public function handle(array $input): ToolResult
    {
        $input = array_values($input);

        if ($this->casts !== null) {
            $input = array_map(fn ($value, $key) => $this->casts[$key]($value), $input, array_keys($input));
        }

        return ($this->handler)(...$input);
    }
}
