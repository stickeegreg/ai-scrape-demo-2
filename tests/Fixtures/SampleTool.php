<?php

namespace Tests\Fixtures;

use App\Tools\Attributes\ToolMethod;
use App\Tools\Attributes\ToolParameter;
use App\Tools\Utils\ToolResult;

class SampleTool
{
    #[ToolMethod('Test Tool 1')]
    public function testTool1(): ToolResult
    {
        return new ToolResult();
    }

    #[ToolMethod('Test Tool 2', 'test_tool_2')]
    public function testTool2(
        #[ToolParameter('The name of the object')]
        string $name
    ): ToolResult {
        return new ToolResult($name);
    }
}
