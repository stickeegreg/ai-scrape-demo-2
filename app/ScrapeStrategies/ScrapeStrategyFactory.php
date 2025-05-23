<?php

namespace App\ScrapeStrategies;

use App\ComputerControllers\ComputerControllerInterface;
use App\ProgressReporters\ProgressReporterInterface;
use App\ScrapeStrategies\ScrapeStrategyInterface;
use Exception;

class ScrapeStrategyFactory
{
    public function create(
        ScrapeStrategy $scrapeStrategy,
        ProgressReporterInterface $progressReporter,
        ComputerControllerInterface $computerController
    ): ScrapeStrategyInterface {
        $anthropicApiKey = config('scrape.anthropic.api_key');

        if (!$anthropicApiKey) {
            throw new Exception('No Anthropic API key set in config scrape.anthropic.api_key');
        }

        return match ($scrapeStrategy) {
            ScrapeStrategy::AnthropicComputerUse => new AnthropicComputerUseScrapeStrategy(
                $progressReporter,
                $computerController,
                $anthropicApiKey,
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
