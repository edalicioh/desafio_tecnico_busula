<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductsFactory extends Factory
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
            'slug' => fake()->slug(),
            'ref' => fake()->unique()->ean13(),
            'description' => fake()->paragraph(),
            'image' => fake()->imageUrl(),
            'price' => fake()->randomFloat(2, 10, 100),
            'category_id' => Category::factory(),
        ];
    }
}
