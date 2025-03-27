<?php

namespace App\Tools;

use App\Tools\Attributes\ToolMethod;
use App\Tools\JsonSchema\JsonSchema;
use App\Tools\ToolInterface;
use App\Tools\ToolResult;
use Closure;
use ReflectionMethod;

class Tool implements ToolInterface
{
    public static function fromMethod(
        object $instance,
        ReflectionMethod $method,
    ): self {
        $toolMethod = ($method->getAttributes(ToolMethod::class)[0] ?? null)?->newInstance();

        $inputSchema = (object) [
            'name' => $toolMethod->name ?? $method->getName(),
            'description' => $toolMethod->description,
            'input_schema' => JsonSchema::fromMethod($method),
        ];
        $handler = $method->getClosure($instance);

        return new self($toolMethod->name ?? $method->getName(), $inputSchema, $handler);
    }

    public function __construct(
        private readonly string $name,
        private readonly object $inputSchema,
        private readonly Closure $handler
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
        return ($this->handler)(...$input);
    }
}
