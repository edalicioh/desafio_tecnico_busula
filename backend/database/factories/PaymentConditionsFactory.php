<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentConditions>
 */
class PaymentConditionsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Immediate Payment', '30 Days', '60 Days', '90 Days', 'Installment Plan']),
            'installments' => fake()->randomElement([1, 3, 6, 12]),
            'discount' => fake()->randomFloat(2, 0, 10),
        ];
    }
}
