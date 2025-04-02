<?php

namespace App\ScrapeStrategies;

use App\Models\ScrapeRun;
use App\ScrapeTypes\ScrapeType;

interface ScrapeStrategyInterface
{
    public function scrape(ScrapeRun $scrapeRun): void;
    public function getNoVncAddress(): string;
}
