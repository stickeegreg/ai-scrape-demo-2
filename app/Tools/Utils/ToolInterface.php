<?php

namespace App\Tools\Utils;

use App\Tools\Utils\ToolResult;

interface ToolInterface
{
    public function getName(): string;

    public function getInputSchema(): object;

    public function handle(array $input): ToolResult;
}
