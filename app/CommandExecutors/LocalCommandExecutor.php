<?php

namespace App\CommandExecutors;

use Exception;
use Illuminate\Support\Facades\Log;

class RemoteCommandExecutor implements CommandExecutorInterface
{
    public function execute(string $command): CommandResult
    {
        Log::info("Running command: $command");
        $descriptorSpec = [
            1 => ['pipe', 'w'], // stdout
            2 => ['pipe', 'w'], // stderr
        ];

        $process = proc_open($command, $descriptorSpec, $pipes);

        if (!is_resource($process)) {
            throw new Exception("Could not execute command: $command");
        }

        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);

        if ($exitCode) {
            Log::debug("Command $command failed with code $exitCode and stderr: $stderr");
            throw new Exception("Command $command failed with code $exitCode and stderr: $stderr");
        }

        return new CommandResult($stdout, $stderr, $exitCode);
    }
}
