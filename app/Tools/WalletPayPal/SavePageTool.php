<?php

namespace App\Tools\WalletPayPal;

use App\Tools\Attributes\ToolMethod;
use App\Tools\Utils\ToolResult;

class SavePageTool
{
    private array $pages = [];

    #[ToolMethod('Save a screenshot of an element.', 'save_element_screenshot')]
    public function savePage(): ToolResult
    {
        return new ToolResult();
    }

    public function getPages(): array
    {
        return $this->pages;
    }
}
