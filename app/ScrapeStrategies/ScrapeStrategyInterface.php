<?php

namespace App\ScrapeStrategies;

use App\Models\ScrapeRun;
use App\ProgressReporters\ProgressReporterInterface;

interface ScrapeStrategyInterface
{
    public static function factory(ProgressReporterInterface $progressReporter): ScrapeStrategyInterface;
    public function scrape(ScrapeRun $scrapeRun): array;
    public function getNoVncAddress(): string;
}
