<template>
  <UCard >
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
        <UInput type="text" id="discount-code" class="w-full p-2 border rounded-l-md" placeholder="XXXXXX" />
        <UButton class="bg-gray-300 text-gray-700 px-4 rounded-r-md hover:bg-gray-400">APPLY</UButton>
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
  </UCard>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useCartStore } from '../store/cartStore';
import { useProductStore } from '../store/productStore';
import type { Product } from '../types/product';

const cartStore = useCartStore();
const productStore = useProductStore();

const shippingCost = 0; // Assuming no separate shipping cost from image
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
  if (!Array.isArray(cartItemsWithProducts.value)) {
    return 0;
  }
  return cartItemsWithProducts.value.reduce((total, item) => {
    const price = parseFloat(item.product.price) || 0;
    return total + (price * item.quantity);
  }, 0);
});

const taxes = computed(() => {
  return (cartSubtotal.value || 0) * taxRate;
});

const cartTotal = computed(() => {
  return (cartSubtotal.value || 0) + (taxes.value || 0);
});
</script>