<?php

namespace App\Tools;

use App\DataRepository;
use App\Tools\Attributes\ToolMethod;
use App\Tools\Attributes\ToolParameter;
use App\Tools\JsonSchema\JsonSchemaArray;
use App\Tools\JsonSchema\JsonSchemaObject;
use App\Tools\ToolResult;

class SaveTextTool implements ToolInterface
{
    public function __construct(
        private DataRepository $dataRepository
    ) {
    }

    #[ToolMethod('Save some text evidence.', 'save_text')]
    public function saveText(
        #[ToolParameter('The text to save.')]
        string $text,
        #[ToolParameter('A number.')]
        int $number = 123,
        #[ToolParameter('An array of TestObjects.', new JsonSchemaArray(new JsonSchemaObject(TestObject::class)))]
        array $objects = []
    ): ToolResult {
        dump('GOT TEXT TO SAVE:', $text);
        $this->dataRepository->addText($text);

        return new ToolResult();
    }
}
