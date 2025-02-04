<?php

namespace App\Tools;

interface ToolInterface
{
    public function run(array $args): ToolResult;
}
