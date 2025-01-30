<?php

namespace App\ScrapeAis;

use App\ScrapeAis\ScrapeAiInterface;

class TempScrapeAi implements ScrapeAiInterface
{
    public function scrape(string $url): array
    {
        return [];
    }
}
