<?php

namespace App\ScrapeStrategies;

use App\ProgressReporters\ProgressReporterInterface;

interface ScrapeStrategyInterface
{
    public static function factory(ProgressReporterInterface $progressReporter): ScrapeStrategyInterface;
    public function scrape(string $url): array;
    public function getNoVncAddress(): string;
}
