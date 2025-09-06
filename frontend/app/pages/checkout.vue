<template>
  <div class="bg-gray-100 min-h-screen py-12">
    <div class="container mx-auto px-4 lg:px-0">
      <div class="lg:flex lg:space-x-12">
        <div class="lg:w-1/2">
          <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <h2 class="text-xl font-semibold mb-4">Billing Address</h2>
            <div class="flex justify-between py-2 border-b">
              <span class="font-medium">Contact</span>
              <span>Scott Windon</span>
            </div>
            <div class="flex justify-between py-2">
              <span class="font-medium">Billing Address</span>
              <span>123 George Street, Sydney, NSW 2000 Australia</span>
            </div>
          </div>

          <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4">Payment</h2>
            <div class="flex items-center justify-between border-b pb-4 mb-4">
              <label for="credit-card" class="flex items-center">
                <input type="radio" id="credit-card" name="payment-method" class="form-radio h-5 w-5 text-blue-600" checked>
                <span class="ml-2 text-gray-700">Credit Card</span>
              </label>
              <div class="flex items-center space-x-2">
                <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Visa_Inc._logo.svg" alt="Visa" class="h-6">
                <img src="https://upload.wikimedia.org/wikipedia/commons/b/b7/MasterCard_Logo.svg" alt="Mastercard" class="h-6">
                <img src="https://upload.wikimedia.org/wikipedia/commons/f/fa/American_Express_logo_%282018%29.svg" alt="American Express" class="h-6">
              </div>
            </div>
            <form>
              <div class="mb-4">
                <label for="card-name" class="block text-sm font-medium text-gray-700 mb-1">Name on card</label>
                <input type="text" id="card-name" class="w-full p-2 border rounded-md" placeholder="John Smith">
              </div>
              <div class="mb-4">
                <label for="card-number" class="block text-sm font-medium text-gray-700 mb-1">Card number</label>
                <input type="text" id="card-number" class="w-full p-2 border rounded-md" placeholder="0000 0000 0000 0000">
              </div>
              <div class="flex space-x-4 mb-4">
                <div class="w-1/2">
                  <label for="expiration-date" class="block text-sm font-medium text-gray-700 mb-1">Expiration date</label>
                  <input type="text" id="expiration-date" class="w-full p-2 border rounded-md" placeholder="MM / YY">
                </div>
                <div class="w-1/2">
                  <label for="security-code" class="block text-sm font-medium text-gray-700 mb-1">Security code</label>
                  <input type="text" id="security-code" class="w-full p-2 border rounded-md" placeholder="000">
                </div>
              </div>
            </form>
            <div class="flex items-center border-t pt-4 mt-4">
              <label for="paypal" class="flex items-center">
                <input type="radio" id="paypal" name="payment-method" class="form-radio h-5 w-5 text-blue-600">
                <img src="https://www.paypalobjects.com/webstatic/mktg/Logo/pp-logo-100px.png" alt="PayPal" class="h-6 ml-2">
              </label>
            </div>
          </div>
        </div>

        <div class="lg:w-1/2 mt-8 lg:mt-0">
          <div class="bg-white p-6 rounded-lg shadow-md">
            <div v-for="item in cartItemsWithProducts" :key="item.id" class="flex justify-between items-center border-b pb-4 mb-4">
              <div class="flex items-center">
                <img :src="item.product.image" class="w-16 h-16 object-cover rounded-md">
                <div class="ml-4">
                  <p class="font-semibold">{{ item.product.name }}</p>
                  <p class="text-sm text-gray-600">x {{ item.quantity }}</p>
                </div>
              </div>
              <p class="font-semibold">${{ (parseFloat(item.product.price) * item.quantity).toFixed(2) }}</p>
            </div>

            <div class="my-4">
              <label for="discount-code" class="block text-sm font-medium text-gray-700 mb-1">Discount code</label>
              <div class="flex">
                <input type="text" id="discount-code" class="w-full p-2 border rounded-l-md" placeholder="XXXXXX">
                <button class="bg-gray-300 text-gray-700 px-4 rounded-r-md hover:bg-gray-400">APPLY</button>
              </div>
            </div>

            <div class="border-t pt-4">
              <div class="flex justify-between mb-2">
                <p class="text-gray-700">Subtotal</p>
                <p class="text-gray-700">${{ cartSubtotal.toFixed(2) }}</p>
              </div>
              <div class="flex justify-between mb-2">
                <p class="text-gray-700">Taxes (GST)</p>
                <p class="text-gray-700">${{ taxes.toFixed(2) }}</p>
              </div>
              <div class="flex justify-between font-bold text-lg">
                <p>Total</p>
                <p>AUD ${{ cartTotal.toFixed(2) }}</p>
              </div>
            </div>

            <button class="w-full bg-blue-600 text-white font-bold py-3 rounded-md mt-6 hover:bg-blue-700 flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
              </svg>
              PAY NOW
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useCartStore } from '../store/cartStore';
import { useProductStore } from '../store/productStore';
import type { Product } from '../types/product';

const cartStore = useCartStore();
const productStore = useProductStore();

const shippingCost = ref(0); // Assuming no separate shipping cost from image
const taxRate = 0.10; // Assuming 10% GST

// Ensure products are loaded
onMounted(async () => {
  if (productStore.products.length === 0) {
    await productStore.fetchProducts();
  }
});

const cartItemsWithProducts = computed(() => {
  return cartStore.cartItems.map(cartItem => {
    const product = productStore.products.find(p => p.id === cartItem.id);
    return {
      ...cartItem,
      product: product || { id: cartItem.id, name: 'Loading...', price: '0', image: '', category: { id: 0, name: '' } }
    };
  });
});

const cartSubtotal = computed(() => {
  return cartItemsWithProducts.value.reduce((total, item) => {
    const price = parseFloat(item.product.price) || 0;
    return total + (price * item.quantity);
  }, 0);
});

const taxes = computed(() => {
  return cartSubtotal.value * taxRate;
});

const cartTotal = computed(() => {
  return cartSubtotal.value + taxes.value;
});
</script>