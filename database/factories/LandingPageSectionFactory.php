<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LandingPageSection>
 */
class LandingPageSectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => fake()->unique()->slug(),
            'name' => fake()->words(2, true),
            'content' => [],
        ];
    }

    /**
     * Create a hero section.
     */
    public function hero(): static
    {
        return $this->state(fn (array $attributes) => [
            'key' => 'hero',
            'name' => 'Hero Section',
            'content' => [
                'arabic_quote' => fake()->sentence(),
                'english_text' => fake()->sentence(),
                'edition' => 'THIRD EDITION',
                'brand_name' => 'علامات',
                'brand_number' => '103',
                'year' => '2026',
            ],
        ]);
    }

    /**
     * Create an about section.
     */
    public function about(): static
    {
        return $this->state(fn (array $attributes) => [
            'key' => 'about',
            'name' => 'About Section',
            'content' => [
                'title' => fake()->sentence(),
                'description_1' => fake()->paragraph(),
                'description_2' => fake()->paragraph(),
            ],
        ]);
    }
}
