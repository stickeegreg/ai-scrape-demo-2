<?php

namespace App\Tools;

use App\CommandExecutors\CommandExecutorInterface;
use Exception;
use Illuminate\Support\Facades\Log;

/*
A tool that allows the agent to interact with the screen, keyboard, and mouse of the current computer.
The tool parameters are defined by Anthropic and are not editable.
*/
class AnthropicComputerUseTool implements ToolInterface
{
    private string $xDoTool;
    private float $screenshotDelay = 0.5;

    public function __construct(
        private CommandExecutorInterface $commandExecutor,
        private int $width,
        private int $height,
        private ?int $displayNum
    ) {
        $this->xDoTool = $this->displayNum ? "DISPLAY=:{$this->displayNum} xdotool" : 'xdotool';
    }

    public function run(array $args): ToolResult
    {
        return $this->executeAction(...$args);
    }

    public function executeAction($action, ?string $text = null, ?array $coordinate = null): ToolResult
    {
        switch ($action) {
            case "mouse_move":
            case "left_click_drag":
                if (!$coordinate || count($coordinate) !== 2) {
                    throw new Exception("Invalid coordinate for action: $action");
                }

                [$x, $y] = $coordinate;

                return $this->runShell("{$this->xDoTool} mousemove --sync $x $y");

            case "key":
                if (!$text) {
                    throw new Exception("Text is required for action: $action");
                }

                return $this->runShell("{$this->xDoTool} key -- $text");

            case "type":
                if (!$text) {
                    throw new Exception("Text is required for action: $action");
                }

                return $this->runShell("{$this->xDoTool} type --delay 12 -- " . escapeshellarg($text));

            case "left_click":
            case "right_click":
            case "middle_click":
            case "double_click":
                $clickArg = [
                    "left_click" => "1",
                    "right_click" => "3",
                    "middle_click" => "2",
                    "double_click" => "--repeat 2 --delay 500 1"
                ][$action];

                return $this->runShell("{$this->xDoTool} click $clickArg");

            case "screenshot":
                return new ToolResult(base64Image: base64_encode($this->takeScreenshot()));
            }

        throw new Exception("Invalid action: $action");
    }

    private function runShell(string $command): ToolResult
    {
        $result = $this->commandExecutor->execute($command);

        return new ToolResult($result->output, $result->exitCode ? $result->error : null);
    }

    private function takeScreenshot(): string
    {
        sleep($this->screenshotDelay);
        // TODO inject url or screenshot strategy
        // TODO error handling
        return file_get_contents("http://localhost:3000/desktop-screenshot");
    }
}

// Example usage:
// $tool = new ComputerTool();
// echo $tool->executeAction("mouse_move", null, [100, 200]);
