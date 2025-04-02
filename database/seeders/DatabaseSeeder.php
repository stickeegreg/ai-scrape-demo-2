<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\ScrapeSeeder;
use Database\Seeders\ScrapeTypeSeeder;
use Database\Seeders\WebsiteSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call([
            WebsiteSeeder::class,
            ScrapeTypeSeeder::class,
            ScrapeSeeder::class,
        ]);
    }
}
