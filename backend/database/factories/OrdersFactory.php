<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Carts;
use App\Models\PaymentMethods;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Orders>
 */
class OrdersFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cart_id' => Carts::factory(),
            'payment_method_id' => PaymentMethods::factory(),
            'total' => fake()->randomFloat(2, 50, 500),
            'status' => fake()->randomElement(['PENDING', 'PAID', 'CANCELED']),
        ];
    }
}
