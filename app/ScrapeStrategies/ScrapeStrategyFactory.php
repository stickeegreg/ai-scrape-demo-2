<?php

namespace App\ScrapeStrategies;

use App\ProgressReporters\ProgressReporterInterface;
use App\ScrapeStrategies\ScrapeStrategyInterface;
use Exception;

class ScrapeStrategyFactory
{
    public function create(ScrapeStrategy $scrapeStrategy, ProgressReporterInterface $progressReporter): ScrapeStrategyInterface
    {
        // TODO: this should take a server from the pool
        $server = config('scrape.servers')[0];
        $anthropicApiKey = config('scrape.anthropic.api_key');

        return match ($scrapeStrategy) {
            ScrapeStrategy::AnthropicComputerUse => new AnthropicComputerUseScrapeStrategy(
                $progressReporter,
                $anthropicApiKey,
                $server['vnc'],
                $server['control']
            ),
            ScrapeStrategy::OpenAIO1WithAnthropicComputerUse => new OpenAIO1WithAnthropicComputerUseScrapeStrategy(
                $progressReporter,
                $anthropicApiKey,
                $server['vnc'],
                $server['control']
            ),
            ScrapeStrategy::OpenAIGPT4oMiniWithAnthropicComputerUse => new OpenAIGPT4oMiniWithAnthropicComputerUseScrapeStrategy(
                $progressReporter,
                $anthropicApiKey,
                $server['vnc'],
                $server['control']
            ),
            default => throw new Exception("Unknown scrape strategy: $scrapeStrategy"),
        };
    }
}
