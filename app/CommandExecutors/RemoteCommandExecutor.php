<?php

namespace App\CommandExecutors;

class RemoteCommandExecutor implements CommandExecutorInterface
{
    public function __construct(
        private readonly string $host
    ) {
    }

    public function execute(string $command): CommandResult
    {
        // TODO this is awful, the worst thing I have ever done
        $result = json_decode(file_get_contents("http://{$this->host}/execute?command=" . urlencode($command)));

        return new CommandResult($result->stdout, $result->stderr, $result->exitCode);
    }
}
