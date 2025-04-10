<?php

namespace App\Tools;

use App\ComputerControllers\ComputerControllerInterface;
use App\ComputerControllers\ScreenshotType;
use App\Tools\Utils\ToolInterface;
use App\Tools\Utils\ToolResult;
use Exception;

/*
A tool that allows the agent to interact with the screen, keyboard, and mouse of the current computer.
The tool parameters are defined by Anthropic and are not editable.
*/
class AnthropicComputerUseTool implements ToolInterface
{
    public function __construct(
        private ComputerControllerInterface $computerController,
        private int $width,
        private int $height,
        private ?int $displayNumber
    ) {
    }

    public function getName(): string
    {
        // NOTE: This name comes from Anthropic and must not be changed.
        return 'computer';
    }

    public static function getDescription(): string
    {
        return 'Interact with the screen, keyboard, and mouse of the current computer.';
    }

    public function getInputSchema(): object
    {
        // This is a special type, not a normal tool
        // See https://docs.anthropic.com/en/docs/build-with-claude/computer-use
        return (object)[
            'type' => 'computer_20241022',
            'name' => 'computer',
            'display_width_px' => $this->width,
            'display_height_px' => $this->height,
            'display_number' => $this->displayNumber,
        ];
    }

    public function handle(array $args): ToolResult
    {
        return $this->executeAction(...$args);
    }

    public function executeAction($action, ?string $text = null, ?array $coordinate = null): ToolResult
    {
        switch ($action) {
            case 'mouse_move':
                if (!$coordinate || count($coordinate) !== 2) {
                    throw new Exception("Invalid coordinate for action: $action");
                }

                [$x, $y] = $coordinate;

                return new ToolResult($this->computerController->moveMouse($x, $y));

            case 'left_click_drag':
                if (!$coordinate || count($coordinate) !== 2) {
                    throw new Exception("Invalid coordinate for action: $action");
                }

                [$x, $y] = $coordinate;

                return new ToolResult($this->computerController->leftClickDrag($x, $y));

            case 'key':
                if (!$text) {
                    throw new Exception("Text is required for action: $action");
                }

                return new ToolResult($this->computerController->key($text));

            case 'type':
                if (!$text) {
                    throw new Exception("Text is required for action: $action");
                }

                return new ToolResult($this->computerController->type($text));

            case 'cursor_position':
                [
                    'x' => $x,
                    'y' => $y,
                ] = $this->computerController->getCursorPosition();

                return new ToolResult("X=$x,Y=$y");

            case 'left_click':
                return new ToolResult($this->computerController->leftClick());

            case 'right_click':
                return new ToolResult($this->computerController->rightClick());

            case 'middle_click':
                return new ToolResult($this->computerController->middleClick());

            case 'double_click':
                return new ToolResult($this->computerController->doubleClick());

            case 'screenshot':
                return new ToolResult(base64Image: base64_encode($this->computerController->getScreenshot(ScreenshotType::SCREEN)));
            }

        throw new Exception("Invalid action: $action");
    }
}
