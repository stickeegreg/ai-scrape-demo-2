<?php

namespace App\ScrapeStrategies;

use App\Models\ScrapeRun;

interface ScrapeStrategyInterface
{
    public function scrape(ScrapeRun $scrapeRun): void;
}
