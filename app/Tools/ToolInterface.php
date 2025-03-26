<?php

namespace App\Tools;

use App\Tools\ToolResult;

interface ToolInterface
{
    public function getName(): string;

    public function getInputSchema(): string;

    public function handle(array $input): ToolResult;
}
