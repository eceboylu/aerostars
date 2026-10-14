<?php

namespace Database\Factories;

use App\Models\Page;
use App\Models\Winner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Winner>
 */
class WinnerFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_page_id' => Page::factory(),
            'company_name' => fake()->company(),
            'verification_slug' => fake()->unique()->slug(),
            'won_at' => '2026-10-24',
            'status' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => false,
        ]);
    }
}
