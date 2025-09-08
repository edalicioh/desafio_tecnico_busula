<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PaymentCondition;
use App\Models\PaymentMethod;

class PaymentConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PaymentCondition::insert([
            [
                //pix
                'name' => 'Immediate Payment',
                'installments' => '1',
                'discount' => -10,
                'payment_method_id' => PaymentMethod::query()->where('name' ,'Pix')->first('id')->id
            ],
            [
                //debit
                'name' => 'Immediate Payment',
                'installments' => '1',
                'discount' => -10,
                'payment_method_id' => PaymentMethod::query()->where('name' ,'Debit Card')->first('id')->id

            ],
            [
                //credit
                'name' => 'Immediate Payment',
                'installments' => '2-12',
                'discount' => 1,
                'payment_method_id' => PaymentMethod::query()->where('name' ,'Credit Card')->first('id')->id

            ],
        ]);
    }
}
