<?php

namespace App\Tools\Attributes;

use App\Tools\JsonSchema\Facades\JsonSchema;
use App\Tools\JsonSchema\JsonSchemaType;
use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class ToolParameter {
    public ?JsonSchemaType $type = null;

    public function __construct(
        public string $description,
        JsonSchemaType|string|null $type = null,
    ) {
        $this->type = is_string($type) ? JsonSchema::fromPhpType($type) : $type;
    }
}
