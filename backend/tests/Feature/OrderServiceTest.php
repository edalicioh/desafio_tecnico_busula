<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Product;
use App\Models\PaymentCondition;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected OrderService $orderService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderService = new OrderService();
    }

    /** @test */
    public function it_can_create_order_from_cart()
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

        $paymentCondition = PaymentCondition::factory()->create([
            'discount' => -10, // 10% discount
            'installments' => '1-12'
        ]);

        $order = $this->orderService->createOrderFromCart($cart, $paymentCondition->id, 1);

        $this->assertNotNull($order);
        $this->assertEquals($cart->id, $order->cart_id);
        $this->assertEquals($paymentCondition->payment_method_id, $order->payment_method_id);
        $this->assertEquals('PENDING', $order->status);
        $this->assertEquals(180.00, $order->total); // 200 - 10% discount

        $this->assertCount(1, $order->items);
        $orderItem = $order->items->first();
        $this->assertEquals($product->id, $orderItem->product_id);
        $this->assertEquals('Test Product', $orderItem->product_name);
        $this->assertEquals(100.00, $orderItem->product_price);
        $this->assertEquals(2, $orderItem->quantity);
        $this->assertEquals(200.00, $orderItem->subtotal);
    }

    /** @test */
    public function it_can_update_order_status()
    {
        $order = Order::factory()->create([
            'status' => 'PENDING'
        ]);

        $updatedOrder = $this->orderService->updateOrderStatus($order, 'PAID');

        $this->assertEquals('PAID', $updatedOrder->status);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'PAID'
        ]);
    }

    /** @test */
    public function it_can_get_order_history()
    {
        $order1 = Order::factory()->create();
        $order2 = Order::factory()->create();

        $orders = $this->orderService->getOrderHistory();

        $this->assertCount(2, $orders);
        $this->assertTrue($orders->contains($order1));
        $this->assertTrue($orders->contains($order2));
    }

    /** @test */
    public function it_can_get_order_history_for_specific_user()
    {
        $userCart = Cart::factory()->create(['user_id' => 1]);
        $userOrder = Order::factory()->create(['cart_id' => $userCart->id]);
        
        $guestCart = Cart::factory()->create(['user_id' => null]);
        $guestOrder = Order::factory()->create(['cart_id' => $guestCart->id]);

        $orders = $this->orderService->getOrderHistory(1);

        $this->assertCount(1, $orders);
        $this->assertTrue($orders->contains($userOrder));
        $this->assertFalse($orders->contains($guestOrder));
    }
}