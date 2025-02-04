<?php

namespace App\ScrapeStrategies;

use App\CommandExecutors\RemoteCommandExecutor;
use App\ProgressReporters\ProgressReporterInterface;
use App\ScrapeStrategies\ScrapeStrategyInterface;
use App\Tools\AnthropicComputerUseTool;
use App\Tools\ToolCollection;

class AnthropicComputerUseScrapeStrategy implements ScrapeStrategyInterface
{
    public static function factory(ProgressReporterInterface $progressReporter): ScrapeStrategyInterface
    {
        // TODO: this should take a VNC server address from the pool
        return new self(config('scrape.anthropic.api_key'), config('scrape.no_vnc_addresses')[0]);
    }

    public function __construct(
        private string $apiKey,
        private string $noVncAddress
    ) {
    }

    public function scrape(string $url): array
    {
        dump($url);
        $commandExecutor = new RemoteCommandExecutor('localhost:3000'); // TODO take from config
        $toolCollection = new ToolCollection([
            // TODO take key from tool
            // TODO "computer" name is from anthropic and must not be changed
            'computer' => new AnthropicComputerUseTool($commandExecutor, 1024, 768, 1), // TODO take from vnc config
        ]);

        // $toolCollection->run('computer', ['mouse_move', null, [100, 100]]);
        $toolCollection->run('computer', ['type', 'This is a test']);
        return [];
    }

    public function getNoVncAddress(): string
    {
        return $this->noVncAddress;
    }
}
