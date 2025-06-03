<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'price' => fake()->randomFloat(2, 10, 99.99),
            'description' => fake()->paragraph(),
            'image' => 'https://picsum.photos/640/480?random='.fake()->randomNumber(),
            'quantity' => fake()->numberBetween(2, 50),
        ];
    }
}
