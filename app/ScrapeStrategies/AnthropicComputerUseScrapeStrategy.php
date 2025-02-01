<?php

namespace App\ScrapeStrategies;

use App\ProgressReporters\ProgressReporterInterface;
use App\ScrapeStrategies\ScrapeStrategyInterface;

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
        return [];
    }

    public function getNoVncAddress(): string
    {
        return $this->noVncAddress;
    }
}
