<?php

namespace App\Tools;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ToolCollection extends Collection
{
    public function run(string $name, array $arguments = []): ToolResult
    {
        $tool = $this->get($name);

        if (!$tool) {
            throw new Exception("Tool not found: $name");
        }

        try {
            return $tool->run($arguments);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw $e;
            return new ToolResult(error: $e->getMessage());
        }
    }
}
