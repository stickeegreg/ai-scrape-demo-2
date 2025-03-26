<?php

namespace Tests\Unit;

use App\Tools\Tool;
use App\Tools\ToolCollection;
use App\Tools\ToolResult;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\SampleTool;

class ToolCollectionTest extends TestCase
{
    public function test_that_it_registers_tools_from_methods(): void
    {
        $toolCollection = new ToolCollection([new SampleTool()]);

        $toolCollection->handle('testTool1');
        $toolCollection->handle('test_tool_2', ['name' => 'test']);

        $this->assertTrue(true);
    }

    public function test_that_it_registers_tools_directly(): void
    {
        $toolCollection = new ToolCollection([new Tool('testTool1', '{"name":"testTool1","description":"","input_schema":{"type":"object"}}', fn () => new ToolResult())]);

        $toolCollection->handle('testTool1');

        $this->assertTrue(true);
    }
}
