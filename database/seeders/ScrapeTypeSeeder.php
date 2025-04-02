<?php

namespace Database\Seeders;

use App\Models\ScrapeType;
use App\ScrapeTypes\ScrapeType as ScrapeTypeEnum;
use Illuminate\Database\Seeder;

class ScrapeTypeSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        ScrapeType::factory()->create([
            'name' => 'Demo',
            'prompt' => '',
            'type' => ScrapeTypeEnum::Demo->value,
        ]);
    }
}
