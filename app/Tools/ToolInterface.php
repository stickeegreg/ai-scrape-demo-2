<?php

namespace App\Tools;

interface ToolInterface
{
    public function run(array $args): ToolResult;
    public static function getName(): string;
    public static function getDescription(): string;
    public static function getInputSchema(): object;
}
