<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class CartController extends Controller
{

    public  function index(string $sessionId)
    {
        $cart = Cart::query()
            ->where('user_id', Auth::user()->id ?? null)
            ->orWhere('session_id', $sessionId)
            ->with(['items', 'items.product'])
            ->orderByDesc('created_at')
            ->first();

        if (!$cart) {
            return response(null, 404);
        }

        $cart['total'] = $cart->items->reduce(fn ($c, $i) => $c + $i->subtotal , 0 );

        return new CartResource($cart);
    }

    public function store(Request $request): CartResource
    {
        $user = Auth::user();
        $sessionId = $request->input('userId');

        if ($user) {
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        } else {
            $cart = Cart::firstOrCreate(['session_id' => $sessionId]);
        }

        $cart->items()->delete();

        $total = 0;

        foreach ($request->input('items') as $item) {
            $product = Product::findOrFail($item['id']);

            $subtotalItem = $product->price * $item['quantity'];


            $cart->items()->create([
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'subtotal' => $subtotalItem,
            ]);

            $total += $subtotalItem;
        }

        $cart['total'] = $total;

        return new CartResource($cart->load('items.product'));
    }
}
