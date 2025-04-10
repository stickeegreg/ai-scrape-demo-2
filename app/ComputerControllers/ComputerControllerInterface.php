<?php

namespace App\ComputerControllers;

use App\ComputerControllers\ScreenshotType;

interface ComputerControllerInterface
{
    public function initialize(string $url): void;
    public function startRecording(): void;
    public function stopRecording(): string;
    public function getScreenshot(ScreenshotType $screenshotType): string;
    public function getRectangleScreenshot(int $left, int $top, int $width, int $height): string;
    public function moveMouse(int $x, int $y): void;
    public function getCursorPosition(): array;
    public function leftClick(?int $x = null, ?int $y = null): void;
    public function rightClick(?int $x = null, ?int $y = null): void;
    public function middleClick(?int $x = null, ?int $y = null): void;
    public function doubleClick(?int $x = null, ?int $y = null): void;
    public function leftClickDrag(int $x, int $y): void;
    public function type(string $text): void;
    public function key(string $key): void;
}
