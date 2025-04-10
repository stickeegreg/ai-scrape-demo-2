<?php

namespace App\Tools\WalletPayPal;

use App\ScrapeTypes\ErrorType;
use App\Tools\Attributes\ToolMethod;
use App\Tools\Attributes\ToolParameter;
use App\Tools\Utils\ToolResult;

class SaveErrorTool
{
    private string $error = '';
    private ?ErrorType $errorType = null;
    private array $errorScreenshots = [];

    #[ToolMethod('Save an error')]
    public function saveError(
        #[ToolParameter('The type of error')]
        ErrorType $errorType,

        #[ToolParameter('The error message')]
        string $errorMessage
    ): ToolResult {
        $this->errorType = $errorType;
        $this->error = $errorMessage;

        return new ToolResult();
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function getErrorType(): ?ErrorType
    {
        return $this->errorType;
    }

    public function getErrorScreenshots(): array
    {
        return $this->errorScreenshots;
    }
}
