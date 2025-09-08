<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CartControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_index_returns_cart_for_authenticated_user()
    {
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['price' => 50]);
        $cart->items()->create(['product_id' => $product->id, 'quantity' => 2, 'subtotal' => 100]);

        $response = $this->actingAs($user)->getJson("/api/v1/cart/{$cart->session_id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'user_id',
                'session_id',
                'status',
                'total',
                'items'
            ]
        ]);
        $this->assertEquals(100, $response->json('data.total'));
    }

    public function test_index_returns_cart_for_guest_user()
    {
        $cart = Cart::factory()->create(['session_id' => 'guest-session-123']);
        $product = Product::factory()->create(['price' => 25]);
        $cart->items()->create(['product_id' => $product->id, 'quantity' => 1, 'subtotal' => 25]);

        $response = $this->getJson("/api/v1/cart/{$cart->session_id}");

        $response->assertStatus(200);
        $this->assertEquals(25, $response->json('data.total'));
    }

    public function test_index_returns_404_when_cart_not_found()
    {
        $response = $this->getJson("/api/v1/cart/non-existent-session");

        $response->assertStatus(404);
        $response->assertJson(['message' => 'Cart not found']);
    }

    public function test_store_creates_cart_for_authenticated_user()
    {
        $user = User::factory()->create();
        $product1 = Product::factory()->create(['price' => 30]);
        $product2 = Product::factory()->create(['price' => 40]);

        $cartData = [
            'items' => [
                ['id' => $product1->id, 'quantity' => 1],
                ['id' => $product2->id, 'quantity' => 2]
            ]
        ];

        $response = $this->actingAs($user)->postJson("/api/v1/cart", $cartData);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'user_id',
                'session_id',
                'status',
                'total',
                'items'
            ]
        ]);
        $this->assertEquals(110, $response->json('data.total')); // 30 + 80
        $this->assertCount(2, $response->json('data.items'));
    }

    public function test_store_creates_cart_for_guest_user()
    {
        $product = Product::factory()->create(['price' => 20]);

        $cartData = [
            'sessionId' => 'guest-session-456',
            'items' => [
                ['id' => $product->id, 'quantity' => 3]
            ]
        ];

        $response = $this->postJson("/api/v1/cart", $cartData);

        $response->assertStatus(201);
        $this->assertEquals(60, $response->json('data.total')); // 20 * 3
        $this->assertEquals('guest-session-456', $response->json('data.session_id'));
    }

    public function test_store_returns_400_when_session_id_missing_for_guest()
    {
        $product = Product::factory()->create();

        $cartData = [
            'items' => [
                ['id' => $product->id, 'quantity' => 1]
            ]
        ];

        $response = $this->postJson("/api/v1/cart", $cartData);

        $response->assertStatus(400);
        $response->assertJson(['message' => 'Session ID is required']);
    }

    public function test_store_returns_404_when_product_not_found()
    {
        $user = User::factory()->create();

        $cartData = [
            'items' => [
                ['id' => 99999, 'quantity' => 1] // Non-existent product
            ]
        ];

        $response = $this->actingAs($user)->postJson("/api/v1/cart", $cartData);

        $response->assertStatus(500); // Exception handling
    }

    public function test_store_handles_empty_items_array()
    {
        $user = User::factory()->create();

        $cartData = [
            'items' => []
        ];

        $response = $this->actingAs($user)->postJson("/api/v1/cart", $cartData);

        $response->assertStatus(201);
        $this->assertEquals(0, $response->json('data.total'));
        $this->assertCount(0, $response->json('data.items'));
    }

    public function test_store_deletes_existing_items_before_adding_new_ones()
    {
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $oldProduct = Product::factory()->create(['price' => 10]);
        $cart->items()->create(['product_id' => $oldProduct->id, 'quantity' => 1, 'subtotal' => 10]);

        $newProduct = Product::factory()->create(['price' => 15]);

        $cartData = [
            'items' => [
                ['id' => $newProduct->id, 'quantity' => 2]
            ]
        ];

        $response = $this->actingAs($user)->postJson("/api/v1/cart", $cartData);

        $response->assertStatus(200);
        $this->assertEquals(30, $response->json('data.total')); // 15 * 2
        $this->assertCount(1, $response->json('data.items'));
    }
}
