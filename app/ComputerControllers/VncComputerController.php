<?php

namespace App\ComputerControllers;

use App\CommandExecutors\CommandExecutorInterface;
use App\CommandExecutors\RemoteCommandExecutor;
use RuntimeException;

class VncComputerController implements ComputerControllerInterface
{
    private readonly string $xDoTool;
    private readonly CommandExecutorInterface $commandExecutor;

    public function __construct(
        private readonly string $noVncAddress,
        private readonly string $controlServiceAddress,
        private readonly int $width,
        private readonly int $height,
        private readonly ?int $displayNumber
    ) {
        $this->commandExecutor = new RemoteCommandExecutor($this->controlServiceAddress);
        $this->xDoTool = $this->displayNumber ? "DISPLAY=:{$this->displayNumber} xdotool" : 'xdotool';
    }

    private function executeCommand(string $command): string
    {
        $result = $this->commandExecutor->execute($command);

        if ($result->exitCode !== 0) {
            throw new RuntimeException("Command failed: $command\n" . $result->error);
        }

        return $result->output;
    }

    public function getNoVncAddress(): string
    {
        return $this->noVncAddress;
    }

    public function initialize(string $url): void
    {
        $this->commandExecutor->execute('/home/stickee/start_chrome.sh ' . escapeshellarg($url));
    }

    public function startRecording(): void
    {
        $this->commandExecutor->execute('/home/stickee/start_recording.sh');
    }

    public function stopRecording(): string
    {
        $this->commandExecutor->execute('/home/stickee/stop_recording.sh');

        // TODO: Let the recording finish, do this a better way
        sleep(5);

        return file_get_contents('http://' . $this->controlServiceAddress . '/get-recording');
    }

    public function getScreenshot(ScreenshotType $screenshotType): string
    {
        sleep(0.5); // TODO: This is a hack to wait for the screenshot to be ready
        // TODO inject url or screenshot strategy
        // TODO error handling
        return file_get_contents('http://' . $this->controlServiceAddress . '/screenshot-desktop');
    }

    public function getRectangleScreenshot(int $left, int $top, int $width, int $height): string
    {
        throw new RuntimeException("Not implemented");
    }

    public function moveMouse(int $x, int $y): void
    {
        $this->executeCommand("{$this->xDoTool} mousemove --sync $x $y");
    }

    public function getCursorPosition(): array
    {
        $result = $this->executeCommand("{$this->xDoTool} getmouselocation --shell");
        $output = explode("\n", trim($result));
        $position = [];

        foreach ($output as $line) {
            [$key, $value] = explode('=', $line);
            $position[$key] = (int)$value;
        }

        return [
            'x' => $position['X'],
            'y' => $position['Y'],
        ];
    }

    public function leftClick(?int $x = null, ?int $y = null): void
    {
        if ($x !== null && $y !== null) {
            $this->moveMouse($x, $y);
        }

        $this->executeCommand("{$this->xDoTool} click 1");
    }

    public function rightClick(?int $x = null, ?int $y = null): void
    {
        if ($x !== null && $y !== null) {
            $this->moveMouse($x, $y);
        }

        $this->executeCommand("{$this->xDoTool} click 3");
    }

    public function middleClick(?int $x = null, ?int $y = null): void
    {
        if ($x !== null && $y !== null) {
            $this->moveMouse($x, $y);
        }

        $this->executeCommand("{$this->xDoTool} click 2");
    }

    public function doubleClick(?int $x = null, ?int $y = null): void
    {
        if ($x !== null && $y !== null) {
            $this->moveMouse($x, $y);
        }

        $this->executeCommand("{$this->xDoTool} click --repeat 2 --delay 500 1");
    }

    public function leftClickDrag(int $x, int $y): void
    {
        $this->executeCommand("{$this->xDoTool} mousedown 1 mousemove --sync $x $y mouseup 1");
    }

    public function type(string $text): void
    {
        $this->executeCommand("{$this->xDoTool} type --delay 12 -- " . escapeshellarg($text));
    }

    public function key(string $key): void
    {
        $this->executeCommand("{$this->xDoTool} key -- " . escapeshellarg($key));
    }
}
