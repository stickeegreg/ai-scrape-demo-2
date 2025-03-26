<?php

namespace App\Tools;

use App\Tools\Attributes\JsonSchemaType;
use App\Tools\Attributes\ToolMethod;
use App\Tools\Attributes\ToolParameter;
use App\Tools\JsonSchema\JsonSchema;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use ReflectionClass;

class ToolCollection extends Collection
{
    /**
     * Create a new collection.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, \App\Tools\ToolInterface>|iterable<TKey, \App\Tools\ToolInterface>|null  $items
     * @return self<TKey, \App\Tools\ToolInterface>
     */
    public static function create($items = [])
    {
        $newItems = [];

        // foreach ($items as $item) {
        //     // $newItems[$item->getName()] = $item;

        //     $reflectionClass = new ReflectionClass($item);
        //     $methods = $reflectionClass->getMethods();


        //     foreach ($methods as $method) {
        //         $attributes = $method->getAttributes(ToolMethod::class);

        //         if ($attributes === []) {
        //             continue;
        //         }

        //         $parameters = $method->getParameters();

        //         /*
        //                 return (object) [
        //     "type" => "object",
        //     "properties" => (object) [
        //         "text" => (object) [
        //             "type" => "string",
        //             "description" => "The text to save.",
        //         ],
        //     ],
        //     "required" => ["text"],
        // ];
        // */

        //         $properties = [];
        //         $required = [];

        //         foreach ($parameters as $parameter) {
        //             if ($parameter->isVariadic()) {
        //                 throw new Exception("Variadic parameters are not supported: " . get_class($item) . '::' . $method->getName() . '() parameter $' . $parameter->getName());
        //             }

        //             $parameterAttributes = $parameter->getAttributes(ToolParameter::class);

        //             if ($parameterAttributes === []) {
        //                 throw new Exception("Missing ToolParameter attribute for: " . get_class($item) . '::' . $method->getName() . '() parameter $' . $parameter->getName());
        //             }

        //             $toolParameter = $parameterAttributes[0]->newInstance();
        //             $type = $toolParameter->type ?? JsonSchema::fromPhpType($parameter->getType()->getName());

        //             $properties[$parameter->getName()] = (object) [
        //                 "type" => $type->jsonSerialize(),
        //                 "description" => $toolParameter->description,
        //             ];

        //             if (!$parameter->isOptional()) {
        //                 $required[] = $parameter->getName();
        //             }
        //         }

        //         $inputSchema = (object) [
        //             "type" => "object",
        //             "properties" => (object) $properties,
        //             "required" => $required,
        //         ];


        //         // dump($inputSchema);
        //     }
        // }



        dd('xxxxxxxx');

        return new self($newItems);
    }

    public function run(string $name, array $arguments = []): ToolResult
    {
        $tool = $this->get($name);

        if (!$tool) {
            throw new Exception("Tool not found: $name");
        }

        try {
            return $tool->run($arguments);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw $e;
            return new ToolResult(error: $e->getMessage());
        }
    }
}
