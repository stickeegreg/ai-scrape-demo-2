<?php

namespace Tests;

use App\Tools\Attributes\ToolFunction;
use App\Tools\Attributes\ToolParameter;
use App\Tools\Utils\ToolResult;

require_once __DIR__ . '/../vendor/autoload.php';

#[ToolFunction('This is a test tool', 'testTool')]
function testTool(
    #[ToolParameter('This is of type int')] int $a,
    #[ToolParameter('This is of type float')] float $b,
    #[ToolParameter('This is of type string')] string $c,
    #[ToolParameter('This is of type bool')] bool $d
): ToolResult {
    return new ToolResult();
}
