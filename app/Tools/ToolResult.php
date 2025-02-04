<?php

namespace App\Tools;

class ToolResult
{
    public function __construct(
        public ?string $output = null,
        public ?string $error = null,
        public ?string $base64Image = null,
        public ?string $system = null
    ) {
    }
}
