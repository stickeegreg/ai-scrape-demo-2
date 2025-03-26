<?php

namespace App\Tools;

use App\DataRepository;
use App\Tools\Attributes\ToolMethod;

class SaveElementScreenshotTool
{
    public function __construct(
        private DataRepository $dataRepository
    ) {
    }

    #[ToolMethod('Save a screenshot of an element.', 'save_element_screenshot')]
    public function saveElementScreenshot(): ToolResult
    {
        dump('GOT SCREENSHOT TO SAVE FOR ELEMENT:');
        // $this->dataRepository->addScreenshot($elementId);

        return new ToolResult();
    }
}
