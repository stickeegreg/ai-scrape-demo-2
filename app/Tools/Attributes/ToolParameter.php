<?php

namespace App\Tools\Attributes;

use App\Tools\JsonSchema\JsonSchemaType;
use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class ToolParameter {
    public function __construct(
        public string $description,
        public JsonSchemaType|null $type = null,
    ) {
    }
}
