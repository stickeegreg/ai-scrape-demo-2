<?php

namespace App\ScrapeStrategies;

use App\ScrapeStrategies\ScrapeStrategyInterface;

class TempScrapeStrategy implements ScrapeStrategyInterface
{
    public function scrape(string $url): array
    {
        return [];
    }
}
