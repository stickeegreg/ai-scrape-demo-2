<?php

namespace App\Tools;

use Exception;

class SaveTextTool implements ToolInterface
{
    public static function getName(): string
    {
        return "save_text";
    }

    public static function getDescription(): string
    {
        return "Save some text evidence.";
    }

    public static function getInputSchema(): object
    {
        return (object) [
            "type" => "object",
            "properties" => (object) [
                "text" => (object) [
                    "type" => "string",
                    "description" => "The text to save.",
                ],
            ],
            "required" => ["text"],
        ];
    }

    public function run(array $args): ToolResult
    {
        $text = $args['text'] ?? null;

        if (!$text) {
            throw new Exception("Text is required.");
        }

        dump('GOT TEXT TO SAVE:', $text);

        return new ToolResult();
    }
}
