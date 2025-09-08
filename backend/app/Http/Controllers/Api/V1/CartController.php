<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\Product;
use App\Models\PaymentCondition;
use App\PaymentMethodEnum;
use App\Services\CartService;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{

    public function index(string $sessionId): JsonResource|JsonResponse
    {
        try {
            $cart = Cart::query()
                ->where(function ($query) use ($sessionId) {
                    $query->where('user_id', Auth::id())
                        ->orWhere('session_id', $sessionId);
                })
                ->with(['items', 'items.product'])
                ->orderByDesc('created_at')
                ->first();

            if (!$cart) {
                return response()->json(['message' => 'Cart not found'], 404);
            }

            $cart->total = $cart->items->sum('subtotal');

            return new CartResource($cart);
        } catch (\Exception $e) {
            logger()->error("Error fetching cart", ['exception' => $e]);
            return response()->json(['message' => 'Error fetching cart'], 500);
        }
    }


    public function store(Request $request): JsonResource|JsonResponse
    {
        try {
            $user = Auth::user();
            $sessionId = $request->input('sessionId');

            if ($user) {
                $cart = Cart::firstOrCreate(['user_id' => $user->id]);
            } else {
                if (!$sessionId) {
                    return response()->json(['message' => 'Session ID is required'], 400);
                }
                $cart = Cart::firstOrCreate(['session_id' => $sessionId]);
            }

            $cart->items()->delete();

            $total = 0;

            foreach ($request->input('items', []) as $item) {
                $product = Product::findOrFail($item['id']);
                $subtotalItem = $product->price * $item['quantity'];

                $cart->items()->create([
                    'product_id' => $item['id'],
                    'quantity'   => $item['quantity'],
                    'subtotal'   => $subtotalItem,
                ]);

                $total += $subtotalItem;
            }

            $cart['total'] = $total;

            return new CartResource($cart->load('items.product'));
        } catch (\Exception $e) {
            logger()->error("Error storing cart", ['exception' => $e]);
            return response()->json(['message' => 'Error storing cart'], 500);
        }
    }

    public function recalculate(int $cartId,  Request $request , CartService $cartService): JsonResource|JsonResponse
    {
        try {
            $conditionId = $request->input('conditionId');
            $installments = $request->input('installments');

            return new CartResource($cartService->recalculate($conditionId,$installments, $cartId,  ));
        } catch (\Exception $e) {
            logger()->error("Error recalculating cart", ['exception' => $e]);
            return response()->json(['message' => 'Error recalculating cart'], 500);
        }
    }
}
