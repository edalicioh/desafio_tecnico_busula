<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentCondition;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createOrderFromCart(Cart $cart, int $paymentConditionId, ?int $installments = null,): Order
    {
        return  DB::transaction(function () use ($cart, $paymentConditionId, $installments) {


            $updatedCart = (new CartService)->recalculate($paymentConditionId, $installments, $cart->id);

            $cart->load('items.product');

            $paymentCondition = PaymentCondition::with('paymentMethod')->findOrFail($paymentConditionId);


            $order = Order::create([
                'cart_id'           => $cart->id,
                'payment_method_id' => $paymentCondition->paymentMethod->id,
                'total'             => $updatedCart->total,
                'status'            => 'PENDING',
            ]);


            $order->items()->createMany(
                $cart->items->map(fn($cartItem) => [
                    'product_id'    => $cartItem->product_id,
                    'product_price' => $cartItem->product->price,
                    'quantity'      => $cartItem->quantity,
                    'subtotal'      => $cartItem->subtotal,
                ])->toArray()
            );

            return $order;
        });
    }

    public function updateOrderStatus(Order $order, string $status): Order
    {
        $order->status = $status;
        $order->save();

        return $order;
    }

    public function getOrderHistory($userId = null)
    {
        $query = Order::with(['items.product', 'paymentMethod', 'cart.user']);

        if ($userId) {
            $query->whereHas('cart', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        }

        return $query->latest()->get();
    }
}
