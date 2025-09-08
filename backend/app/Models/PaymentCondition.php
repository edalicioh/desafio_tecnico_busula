<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentCondition extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentConditionsFactory> */
    use HasFactory, SoftDeletes;

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
