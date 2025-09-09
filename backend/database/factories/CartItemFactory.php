<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Cart;
use App\Models\Product;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CartItem>
 */
class CartItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Create a product first to get its price
        $product = Product::all()->random();
        $quantity = fake()->numberBetween(1, 5);
        $subtotal = $product->price * $quantity;

        return [
            'cart_id' => Cart::all()->random()->id,
            'product_id' => $product->id,
            'quantity' => $quantity,
            'subtotal' => $subtotal,
        ];
    }
}
