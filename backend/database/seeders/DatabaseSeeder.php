<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // First, seed the independent entities
        $this->call([
            CategorySeeder::class,
            PaymentMethodSeeder::class,
            PaymentConditionSeeder::class,
        ]);

        // Then seed products which depend on categories
        $this->call(ProductSeeder::class);

        // Create a test user if one doesn't exist
        if (User::count() == 0) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }

        // Seed the cart-related entities
        $this->call([
            CartSeeder::class,
            CartItemSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
