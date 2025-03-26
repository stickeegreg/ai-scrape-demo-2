<?php

namespace App\Tools\Attributes;

use App\Tools\JsonSchema\JsonSchemaType;
use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ToolProperty {
    public function __construct(
        public string $description,
        public JsonSchemaType|null $type = null,
    ) {
    }
}
