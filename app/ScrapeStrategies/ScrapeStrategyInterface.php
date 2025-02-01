<?php

namespace App\ScrapeStrategies;

interface ScrapeStrategyInterface
{
    public function scrape(string $url): array;
}
