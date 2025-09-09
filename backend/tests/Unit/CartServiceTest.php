<?php

namespace Tests\Unit;

use App\Enums\PaymentMethodEnum;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\PaymentCondition;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Category;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $cartService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cartService = new CartService();
    }

    /**
     * Helper method to create test data
     */
    private function createTestData($subtotal = 100, $paymentMethodId = PaymentMethodEnum::CREDIT_CARD->value, $discount = 5)
    {
        // Create category first (required by product)
        $category = Category::create([
            'name' => 'Test Category',
        ]);

        // Create payment method
        $paymentMethod = PaymentMethod::create([
            'id' => $paymentMethodId,
            'name' => 'Test Payment Method',
        ]);

        // Create payment condition
        $paymentCondition = PaymentCondition::create([
            'payment_method_id' => $paymentMethod->id,
            'name' => 'Test Condition',
            'discount' => $discount,
            'installments' => 12,
        ]);

        // Create product
        $product = Product::create([
            'name' => 'Test Product',
            'slug' => 'test-product',
            'ref' => 'TEST-001',
            'description' => 'Test description',
            'image' => 'test-image.jpg',
            'price' => $subtotal,
            'category_id' => $category->id,
        ]);

        // Create cart with items
        $cart = Cart::create([
            'user_id' => null,
            'session_id' => 'test-session',
        ]);

        $cartItem = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'subtotal' => $subtotal,
        ]);

        return [
            'cart' => $cart,
            'paymentCondition' => $paymentCondition,
            'paymentMethod' => $paymentMethod,
            'product' => $product,
            'cartItem' => $cartItem,
        ];
    }

    public function test_recalculates_with_credit_card_and_compound_interest()
    {
        // Arrange
        $data = $this->createTestData(100, PaymentMethodEnum::CREDIT_CARD->value, 1.5);
        $installments = 3;
        $expectedTotal = 100 * pow(1 + 1.5/100, $installments); // 104.5678375

        // Act
        $result = $this->cartService->recalculate($data['paymentCondition']->id, $installments, $data['cart']->id);

        // Assert
        $this->assertEquals($expectedTotal, $result->total, '', 0.0001);
        $this->assertEquals($data['cart']->id, $result->id);
    }

    public function test_recalculates_with_credit_card_single_installment()
    {
        // Arrange
        $data = $this->createTestData(100, PaymentMethodEnum::CREDIT_CARD->value, 5);
        $installments = 1;
        $expectedTotal = 100 * (1 + 5/100); // 105.00

        // Act
        $result = $this->cartService->recalculate($data['paymentCondition']->id, $installments, $data['cart']->id);

        // Assert
        $this->assertEquals($expectedTotal, $result->total, '', 0.0001);
    }

    public function test_recalculates_with_pix_and_discount()
    {
        // Arrange
        $data = $this->createTestData(100, PaymentMethodEnum::PIX->value, 10);
        $installments = 1;
        $expectedTotal = 100 * (1 - 10/100); // 90.00

        // Act
        $result = $this->cartService->recalculate($data['paymentCondition']->id, $installments, $data['cart']->id);

        // Assert
        $this->assertEquals($expectedTotal, $result->total, '', 0.0001);
    }

    public function test_recalculates_with_debit_card_and_discount()
    {
        // Arrange
        $data = $this->createTestData(100, PaymentMethodEnum::DEBIT_CARD->value, 5);
        $installments = 1;
        $expectedTotal = 100 * (1 - 5/100); // 95.00

        // Act
        $result = $this->cartService->recalculate($data['paymentCondition']->id, $installments, $data['cart']->id);

        // Assert
        $this->assertEquals($expectedTotal, $result->total, '', 0.0001);
    }

    public function test_recalculates_with_zero_subtotal()
    {
        // Arrange
        $data = $this->createTestData(0, PaymentMethodEnum::CREDIT_CARD->value, 5);
        $installments = 1;
        $expectedTotal = 0; // 0 * (1 + 5/100) = 0

        // Act
        $result = $this->cartService->recalculate($data['paymentCondition']->id, $installments, $data['cart']->id);

        // Assert
        $this->assertEquals($expectedTotal, $result->total, '', 0.0001);
    }

    public function test_recalculates_with_pix_zero_discount()
    {
        // Arrange
        $data = $this->createTestData(100, PaymentMethodEnum::PIX->value, 0);
        $installments = 1;
        $expectedTotal = 100; // 100 * (1 - 0/100) = 100

        // Act
        $result = $this->cartService->recalculate($data['paymentCondition']->id, $installments, $data['cart']->id);

        // Assert
        $this->assertEquals($expectedTotal, $result->total, '', 0.0001);
    }

    public function test_recalculates_with_negative_discount_pix()
    {
        // Arrange
        $data = $this->createTestData(100, PaymentMethodEnum::PIX->value, -5);
        $installments = 1;
        $expectedTotal = 100 * (1 - (-5/100)); // 105.00

        // Act
        $result = $this->cartService->recalculate($data['paymentCondition']->id, $installments, $data['cart']->id);

        // Assert
        $this->assertEquals($expectedTotal, $result->total, '', 0.0001);
    }

    public function test_recalculates_with_100_percent_discount_pix()
    {
        // Arrange
        $data = $this->createTestData(100, PaymentMethodEnum::PIX->value, 100);
        $installments = 1;
        $expectedTotal = 0; // 100 * (1 - 100/100) = 0

        // Act
        $result = $this->cartService->recalculate($data['paymentCondition']->id, $installments, $data['cart']->id);

        // Assert
        $this->assertEquals($expectedTotal, $result->total, '', 0.0001);
    }

    public function test_recalculates_with_default_payment_method()
    {
        // Arrange
        $data = $this->createTestData(150, 999, 0); // Non-existent payment method
        $installments = 1;
        $expectedTotal = 150; // Default case should return subtotal

        // Act
        $result = $this->cartService->recalculate($data['paymentCondition']->id, $installments, $data['cart']->id);

        // Assert
        $this->assertEquals($expectedTotal, $result->total, '', 0.0001);
    }

    public function test_recalculates_with_invalid_cart_id()
    {
        // Arrange
        $data = $this->createTestData(100);
        $invalidCartId = 999;

        // Act & Assert
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $this->cartService->recalculate($data['paymentCondition']->id, 1, $invalidCartId);
    }

    public function test_recalculates_with_invalid_condition_id()
    {
        // Arrange
        $data = $this->createTestData(100);
        $invalidConditionId = 999;

        // Act & Assert
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $this->cartService->recalculate($invalidConditionId, 1, $data['cart']->id);
    }

    public function test_recalculates_with_multiple_items()
    {
        // Arrange
        $data = $this->createTestData(50, PaymentMethodEnum::PIX->value, 10);
        
        // Add another item to the cart
        $category = Category::create([
            'name' => 'Second Category',
        ]);

        $product2 = Product::create([
            'name' => 'Second Product',
            'slug' => 'second-product',
            'ref' => 'TEST-002',
            'description' => 'Test description',
            'image' => 'test-image-2.jpg',
            'price' => 75,
            'category_id' => $category->id,
        ]);

        CartItem::create([
            'cart_id' => $data['cart']->id,
            'product_id' => $product2->id,
            'quantity' => 1,
            'subtotal' => 75,
        ]);

        $installments = 1;
        $expectedTotal = (50 + 75) * (1 - 10/100); // 112.50

        // Act
        $result = $this->cartService->recalculate($data['paymentCondition']->id, $installments, $data['cart']->id);

        // Assert
        $this->assertEquals($expectedTotal, $result->total, '', 0.0001);
    }

    public function test_recalculates_with_high_installments_credit_card()
    {
        // Arrange
        $data = $this->createTestData(100, PaymentMethodEnum::CREDIT_CARD->value, 1.5);
        $installments = 12;
        $expectedTotal = 100 * pow(1 + 1.5/100, $installments);

        // Act
        $result = $this->cartService->recalculate($data['paymentCondition']->id, $installments, $data['cart']->id);

        // Assert
        $this->assertEquals($expectedTotal, $result->total, '', 0.0001);
    }

    public function test_recalculates_with_zero_installments_credit_card()
    {
        // Arrange
        $data = $this->createTestData(100, PaymentMethodEnum::CREDIT_CARD->value, 5);
        $installments = 0;
        $expectedTotal = 100; // 100 * (1 + 5/100)^0 = 100

        // Act
        $result = $this->cartService->recalculate($data['paymentCondition']->id, $installments, $data['cart']->id);

        // Assert
        $this->assertEquals($expectedTotal, $result->total, '', 0.0001);
    }

    public function test_recalculates_with_negative_installments()
    {
        // Arrange
        $data = $this->createTestData(100, PaymentMethodEnum::CREDIT_CARD->value, 5);
        $installments = -1;
        $expectedTotal = 100 * pow(1 + 5/100, $installments);

        // Act
        $result = $this->cartService->recalculate($data['paymentCondition']->id, $installments, $data['cart']->id);

        // Assert
        $this->assertEquals($expectedTotal, $result->total, '', 0.0001);
    }

    public function test_recalculates_with_100_percent_discount_credit_card()
    {
        // Arrange
        $data = $this->createTestData(100, PaymentMethodEnum::CREDIT_CARD->value, 100);
        $installments = 3;
        $expectedTotal = 100 * pow(1 + 100/100, $installments); // 100 * 2^3 = 800

        // Act
        $result = $this->cartService->recalculate($data['paymentCondition']->id, $installments, $data['cart']->id);

        // Assert
        $this->assertEquals($expectedTotal, $result->total, '', 0.0001);
    }
}