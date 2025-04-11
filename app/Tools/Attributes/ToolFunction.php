<?php

namespace App\Tools\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_FUNCTION)]
class ToolFunction {
    public function __construct(
        public string $description,
        public ?string $name = null,
    ) {
    }
}
