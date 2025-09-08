<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;
use App\Enums\PaymentMethodEnum;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PaymentMethod::insert([
            ['name' => 'Credit Card', 'id' => PaymentMethodEnum::CREDIT_CARD],
            ['name' => 'Debit Card', 'id' => PaymentMethodEnum::DEBIT_CARD],
            ['name' => 'Pix', 'id' => PaymentMethodEnum::PIX],
        ]);
    }
}
