<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;

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
        // Tech-themed image URLs for seeding
        $techImages = [
            'https://placehold.co/600x400/3b82f6/ffffff?text=Laptop',
            'https://placehold.co/600x400/ef4444/ffffff?text=Smartphone',
            'https://placehold.co/600x400/10b981/ffffff?text=Headphones',
            'https://placehold.co/600x400/f59e0b/ffffff?text=Camera',
            'https://placehold.co/600x400/8b5cf6/ffffff?text=Smartwatch',
            'https://placehold.co/600x400/ec4899/ffffff?text=Drone',
            'https://placehold.co/600x400/06b6d4/ffffff?text=VR+Headset',
            'https://placehold.co/600x40/84cc16/ffffff?text=Gaming+Console',
            'https://placehold.co/600x400/f97316/ffffff?text=Keyboard',
            'https://placehold.co/600x400/64748b/ffffff?text=Server+Rack'
        ];

        return [
            'name' => fake()->words(3, true),
            'slug' => fake()->slug(),
            'ref' => fake()->unique()->ean13(),
            'description' => fake()->paragraph(),
            'image' => fake()->randomElement($techImages),
            'price' => fake()->randomFloat(2, 10, 100),
            'category_id' => Category::all()->random()->id,
        ];
    }
}
