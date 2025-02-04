<?php

namespace App\CommandExecutors;

interface CommandExecutorInterface
{
    public function execute(string $command): CommandResult;
}
