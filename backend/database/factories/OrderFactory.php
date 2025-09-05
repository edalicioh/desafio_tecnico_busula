<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Cart;
use App\Models\PaymentMethod;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cart_id' => Cart::all()->random()->id,
            'payment_method_id' => PaymentMethod::all()->random()->id,
            'total' => fake()->randomFloat(2, 50, 500),
            'status' => fake()->randomElement(['PENDING', 'PAID', 'CANCELED']),
        ];
    }
}
