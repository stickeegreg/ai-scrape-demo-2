<?php

namespace Database\Factories;

use App\ScrapeTypes\ScrapeType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ScrapeType>
 */
class ScrapeTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->name(),
            'prompt' => '',
            'type' => fake()->randomElement(array_map(fn (ScrapeType $t): string => $t->value, ScrapeType::cases())),
        ];
    }
}
