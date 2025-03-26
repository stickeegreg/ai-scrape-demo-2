<?php

namespace App\Tools;

use App\Tools\Attributes\ToolMethod;
use App\Tools\JsonSchema\JsonSchema;
use Exception;
use Illuminate\Support\Facades\Log;
use ReflectionClass;
use ReflectionMethod;

class ToolCollection
{
    private array $items = [];

    public function __construct(array $items)
    {
        foreach ($items as $item) {
            $this->register($item);
        }
    }

    public function addItem(string $name, Tool $tool): void
    {
        if (isset($this->items[$name])) {
            throw new Exception("Tool name already exists: $name");
        }

        $this->items[$name] = $tool;
    }

    private function register(object $item): void
    {
        if ($item instanceof Tool) {
            $this->addItem($item->getName(), $item);

            return;
        }

        $reflectionClass = new ReflectionClass($item);
        $methods = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            $toolMethod = $method->getAttributes(ToolMethod::class)[0]?->newInstance();

            if (!$toolMethod) {
                continue;
            }

            $name = $toolMethod->name ?? $method->getName();

            $this->addItem($name, Tool::fromMethod($item, $method));
        }
    }

    public function handle(string $name, array $arguments = []): ToolResult
    {
        $tool = $this->items[$name] ?? null;

        if (!$tool) {
            throw new Exception("Tool not found: $name");
        }

        try {
            return $tool->handle($arguments);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw $e;
            return new ToolResult(error: $e->getMessage());
        }
    }

    public function getInputSchemas(): array
    {
        return array_map(fn ($tool) => $tool->getInputSchema(), $this->items);
    }
}
