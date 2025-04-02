<?php

namespace Database\Factories;

use App\ScrapeStrategies\ScrapeStrategy;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Scrape>
 */
class ScrapeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'url' => fake()->unique()->url(),
            'prompt' => '',
            'strategy' => fake()->randomElement(array_map(fn (ScrapeStrategy $s): string => $s->value, ScrapeStrategy::cases())),
        ];
    }
}
