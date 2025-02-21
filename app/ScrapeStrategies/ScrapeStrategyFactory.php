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

        switch ($scrapeStrategy) {
            case ScrapeStrategy::AnthropicComputerUse:
                return new AnthropicComputerUseScrapeStrategy($progressReporter, config('scrape.anthropic.api_key'), $server['vnc'], $server['control']);

            case ScrapeStrategy::OpenAIO1WithAnthropicComputerUse:
                return new OpenAIO1WithAnthropicComputerUseScrapeStrategy($progressReporter, config('scrape.anthropic.api_key'), $server['vnc'], $server['control']);

            case ScrapeStrategy::OpenAIGPT4oMiniWithAnthropicComputerUse:
                return new OpenAIGPT4oMiniWithAnthropicComputerUseScrapeStrategy($progressReporter, config('scrape.anthropic.api_key'), $server['vnc'], $server['control']);

            default:
                throw new Exception("Unknown scrape strategy: $scrapeStrategy");
        }
    }
}
