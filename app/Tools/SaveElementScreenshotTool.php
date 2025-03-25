<?php

namespace App\Tools;

use App\DataRepository;
use Exception;

class SaveElementScreenshotTool implements ToolInterface
{
    public static function getName(): string
    {
        return "save_element_screenshot";
    }

    public static function getDescription(): string
    {
        return "Save a screenshot of an element.";
    }

    public static function getInputSchema(): object
    {
        return (object) [
            "type" => "object",
            "properties" => (object) [],
            "required" => [],
        ];
    }

    public function __construct(
        private DataRepository $dataRepository
    ) {
    }

    public function run(array $args): ToolResult
    {
        dump('GOT SCREENSHOT TO SAVE:', $args);
        // $this->dataRepository->addScreenshot($text);

        return new ToolResult();
    }
}
