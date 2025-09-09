<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Cart;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function store(Request $request): JsonResponse|OrderResource
    {
        try {
            $validated = $request->validate([
                'cart_id' => 'required|exists:carts,id',
                'payment_condition_id' => 'required|exists:payment_conditions,id',
                'installments' => 'nullable|integer|min:1|max:12'
            ]);

            $cart = Cart::where('id' ,$validated['cart_id'])
                ->where('status', 'OPEN')
                ->get()
                ->first();

            if ($cart->items->isEmpty()) {
                return response()->json(['message' => 'Cart is empty'], 400);
            }
            $order = $this->orderService->createOrderFromCart(
                $cart,
                $validated['payment_condition_id'],
                $validated['installments'] ?? null
            );

            $cart->update([
                'status' => 'CHECKOUT'
            ]);

            return new OrderResource($order);
        } catch (\Exception $e) {
            logger()->error("Error order", ['exception' => $e]);
            return response()->json(['message' => 'Error order'], 500);
        }
    }
}
