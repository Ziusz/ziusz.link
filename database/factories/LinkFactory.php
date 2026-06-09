<?php

namespace Database\Factories;

use App\Link;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Link>
 */
class LinkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->slug(),
            'destination_url' => fake()->url(),
            'title' => fake()->words(3, true),
            'description' => fake()->optional()->sentence(),
            'is_active' => true,
            'is_listed' => true,
            'sort_order' => 0,
        ];
    }
}
