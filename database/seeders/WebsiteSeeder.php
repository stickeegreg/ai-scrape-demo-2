<?php

namespace Database\Seeders;

use App\Models\Website;
use Illuminate\Database\Seeder;

class WebsiteSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Website::factory()->create([
            'name' => 'Mr Zebra',
            'url' => 'https://zebra-north.com',
        ]);
        Website::factory()->create([
            'name' => 'Amazon UK',
            'url' => 'https://www.amazon.co.uk',
            'prompt' => 'You MUST close all popups, either by accepting cookies, or clicking close or the X icon, before doing anything else.',
        ]);
    }
}
