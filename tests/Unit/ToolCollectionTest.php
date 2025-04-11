<?php

namespace Tests\Unit;

use App\Tools\Attributes\ToolFunction;
use App\Tools\Attributes\ToolParameter;
use App\Tools\Utils\Tool;
use App\Tools\Utils\ToolCollection;
use App\Tools\Utils\ToolResult;
use Exception;
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

    public function test_that_it_registers_tools_from_functions(): void
    {
        $toolCollection = new ToolCollection(['\\Tests\\testTool']);

        $toolCollection->handle('testTool', [1, 1.0, 'test', true]);

        $this->assertTrue(true);
    }

    public function test_that_it_registers_tools_from_lambda_functions(): void
    {
        $f = #[ToolFunction('This is a test tool', 'testTool')]
            function (
                #[ToolParameter('This is of type int')] int $a,
                #[ToolParameter('This is of type float')] float $b,
                #[ToolParameter('This is of type string')] string $c,
                #[ToolParameter('This is of type bool')] bool $d
            ): ToolResult {
                return new ToolResult();
            };

        $toolCollection = new ToolCollection([$f]);

        $toolCollection->handle('testTool', [1, 1.0, 'test', true]);

        $this->assertTrue(true);
    }

    public function test_that_lambda_functions_must_be_named(): void
    {
        $this->expectException(Exception::class);

        $f = #[ToolFunction('This is a test tool')]
            function (
                #[ToolParameter('This is of type int')] int $a,
                #[ToolParameter('This is of type float')] float $b,
                #[ToolParameter('This is of type string')] string $c,
                #[ToolParameter('This is of type bool')] bool $d
            ): ToolResult {
                return new ToolResult();
            };

        new ToolCollection([$f]);
    }

    public function test_that_it_registers_tools_directly(): void
    {
        $toolCollection = new ToolCollection([new Tool('testTool1', (object) ["name" => "testTool1", "description" => "", "input_schema" => ["type" => "object"]], fn () => new ToolResult())]);

        $toolCollection->handle('testTool1');

        $this->assertTrue(true);
    }
}
