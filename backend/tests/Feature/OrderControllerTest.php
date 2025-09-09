<?php

use App\Models\Order;
use App\Models\Cart;
use App\Models\Product;
use App\Models\PaymentCondition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_order()
    {
        $product = Product::factory()->create([
            'name' => 'Test Product',
            'price' => 100.00
        ]);

        $cart = Cart::factory()->create();
        $cart->items()->create([
            'product_id' => $product->id,
            'quantity' => 2,
            'subtotal' => 200.00
        ]);

        $paymentCondition = PaymentCondition::factory()->create();

        $response = $this->postJson('/api/v1/orders/checkout', [
            'cart_id' => $cart->id,
            'payment_condition_id' => $paymentCondition->id,
            'installments' => 1
        ]);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'message',
                    'order' => [
                        'id',
                        'cart_id',
                        'payment_method_id',
                        'total',
                        'status',
                        'items'
                    ]
                ]);

        $this->assertDatabaseHas('orders', [
            'cart_id' => $cart->id,
            'status' => 'PENDING'
        ]);
    }

    /** @test */
    public function it_cannot_create_order_with_empty_cart()
    {
        $cart = Cart::factory()->create();
        $paymentCondition = PaymentCondition::factory()->create();

        $response = $this->postJson('/api/v1/orders/checkout', [
            'cart_id' => $cart->id,
            'payment_condition_id' => $paymentCondition->id
        ]);

        $response->assertStatus(400)
                ->assertJson(['message' => 'Cart is empty']);
    }

    /** @test */
    public function it_can_show_order()
    {
        $order = Order::factory()->create();

        $response = $this->getJson("/api/v1/orders/{$order->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'id' => $order->id,
                    'cart_id' => $order->cart_id,
                    'payment_method_id' => $order->payment_method_id,
                    'total' => $order->total,
                    'status' => $order->status
                ]);
    }

    /** @test */
    public function it_can_update_order_status()
    {
        $order = Order::factory()->create(['status' => 'PENDING']);

        $response = $this->putJson("/api/v1/orders/{$order->id}", [
            'status' => 'PAID'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Order status updated successfully',
                    'order' => [
                        'id' => $order->id,
                        'status' => 'PAID'
                    ]
                ]);
    }

    /** @test */
    public function it_validates_order_creation_data()
    {
        $response = $this->postJson('/api/v1/orders/checkout', [
            'cart_id' => 'invalid',
            'payment_condition_id' => 'invalid'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['cart_id', 'payment_condition_id']);
    }
}