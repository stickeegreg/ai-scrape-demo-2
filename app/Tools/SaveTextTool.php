<?php

namespace App\Tools;

use App\DataRepository;
use App\Tools\Attributes\ToolMethod;
use App\Tools\Attributes\ToolParameter;
use App\Tools\ToolResult;

class SaveTextTool
{
    public function __construct(
        private DataRepository $dataRepository
    ) {
    }

    #[ToolMethod('Save some text evidence.', 'save_text')]
    public function saveText(
        #[ToolParameter('The text to save.')]
        string $text
    ): ToolResult {
        dump('GOT TEXT TO SAVE:', $text);
        $this->dataRepository->addText($text);

        return new ToolResult();
    }
}
