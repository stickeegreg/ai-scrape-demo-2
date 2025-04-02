<?php

namespace Database\Seeders;

use App\Models\Scrape;
use App\Models\ScrapeType;
use App\Models\Website;
use App\ScrapeStrategies\ScrapeStrategy;
use App\ScrapeTypes\ScrapeType as ScrapeTypeEnum;
use Illuminate\Database\Seeder;

class ScrapeSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Scrape::factory()->create([
            'website_id' => Website::where('url', 'https://zebra-north.com')->first()->id,
            'scrape_type_id' => ScrapeType::where('type', ScrapeTypeEnum::Demo->value)->first()->id,
            'url' => 'https://zebra-north.com/code/',
            'prompt' => 'Navigate to the C++ page and use the `save_text` tool to save the text from each article title. Call the tool once for each title.',
            'strategy' => ScrapeStrategy::AnthropicComputerUse->value,
        ]);

        Scrape::factory()->create([
            'website_id' => Website::where('url', 'https://www.amazon.co.uk')->first()->id,
            'scrape_type_id' => ScrapeType::where('type', ScrapeTypeEnum::Demo->value)->first()->id,
            'url' => 'https://www.amazon.co.uk',
            'prompt' => 'Find a My Little Pony soft toy, go to the product page and use the `save_text` tool to save the name, then again to save the price, and then again to save the description.',
            'strategy' => ScrapeStrategy::AnthropicComputerUse->value,
        ]);
    }
}
