<?php

namespace App\CommandExecutors;

class CommandResult
{
    public function __construct(
        public readonly string $output,
        public readonly string $error,
        public readonly int $exitCode,
    ) {
    }
}
