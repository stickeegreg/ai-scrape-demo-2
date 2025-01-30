<?php

namespace App\ScrapeAis;

interface ScrapeAiInterface
{
    public function scrape(string $url): array;
}
