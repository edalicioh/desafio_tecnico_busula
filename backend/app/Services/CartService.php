<?php

namespace App\Services;

use App\Enums\PaymentMethodEnum;
use App\Models\Cart;
use App\Models\PaymentCondition;

class CartService
{
    public function recalculate($conditionId, $installments, $cartId)
    {
        $cart = Cart::findOrFail($cartId);

        $paymentCondition = PaymentCondition::with('paymentMethod')->findOrFail($conditionId);


        $subtotal = $cart->items->sum('subtotal');

        switch ($paymentCondition->paymentMethod->id) {
            case PaymentMethodEnum::CREDIT_CARD->value:
                $cart->total = $subtotal * pow(1 + ($paymentCondition->discount / 100), $installments);
                break;
            case PaymentMethodEnum::PIX->value:
            case PaymentMethodEnum::DEBIT_CARD->value:
                $cart->total = $subtotal * (1 - ($paymentCondition->discount / 100));
                break;

            default:
                $cart->total = $subtotal;
                break;
        }


        $cart->save();

        return $cart->load('items.product');
    }
}
