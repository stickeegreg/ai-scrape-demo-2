<?php

namespace App\Tools;

use App\Tools\Attributes\ToolMethod;
use App\Tools\ToolInterface;
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

    public function addItem(string $name, ToolInterface $tool): void
    {
        if (isset($this->items[$name])) {
            throw new Exception("Tool name already exists: $name");
        }

        $this->items[$name] = $tool;
    }

    private function register(object $item): void
    {
        if ($item instanceof ToolInterface) {
            $this->addItem($item->getName(), $item);

            return;
        }

        $reflectionClass = new ReflectionClass($item);
        $methods = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);
        $hasToolMethods = false;

        foreach ($methods as $method) {
            $toolMethod = ($method->getAttributes(ToolMethod::class)[0] ?? null)?->newInstance();

            if (!$toolMethod) {
                continue;
            }

            $hasToolMethods = true;
            $name = $toolMethod->name ?? $method->getName();

            $this->addItem($name, Tool::fromMethod($item, $method));
        }

        if (!$hasToolMethods) {
            throw new Exception('No tool methods found on ' . get_class($item));
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

    public function getJsonSchemas(): array
    {
        return array_values(array_map(fn ($tool) => $tool->getInputSchema(), $this->items));
    }
}
