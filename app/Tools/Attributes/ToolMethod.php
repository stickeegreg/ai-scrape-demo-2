<?php

namespace App\Tools\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class ToolMethod {
    public function __construct(
        public string $description,
        public ?string $name = null,
    ) {
    }
}
