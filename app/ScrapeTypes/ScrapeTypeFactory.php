<?php

namespace App\ScrapeTypes;

use App\Models\ScrapeRun;
use App\ScrapeTypes\ScrapeType;
use Exception;

class ScrapeTypeFactory
{
    public function create(ScrapeType $scrapeType, string $prompt, ScrapeRun $scrapeRun): ScrapeTypeInterface
    {
        return match ($scrapeType) {
            ScrapeType::Demo => new DemoScrapeType($prompt, $scrapeRun),
            ScrapeType::WalletPayPal => new WalletPayPalScrapeType($prompt),
            default => throw new Exception("Unknown scrape type: $scrapeType"),
        };
    }
}
