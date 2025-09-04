<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Carts;
use App\Models\Products;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CartItems>
 */
class CartItemsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Create a product first to get its price
        $product = Products::factory()->create();
        $quantity = fake()->numberBetween(1, 5);
        $subtotal = $product->price * $quantity;

        return [
            'cart_id' => Carts::factory(),
            'product_id' => $product->id,
            'quantity' => $quantity,
            'subtotal' => $subtotal,
        ];
    }
}
