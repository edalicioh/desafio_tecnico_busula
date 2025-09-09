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
      <p class="font-semibold">${{ (+item.product.price * item.quantity).toFixed(2) }}</p>
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
      <div v-if="cartTotal < cartSubtotal" class="flex justify-between mb-2">
        <p class="text-gray-700">Discount</p>
        <p class="text-green-500">-${{ (cartSubtotal - cartTotal).toFixed(2) }}</p>
      </div>
      <div v-else-if="cartTotal > cartSubtotal" class="flex justify-between mb-2">
        <p class="text-gray-700">Fees</p>
        <p class="text-red-500">+${{ (cartTotal - cartSubtotal).toFixed(2) }}</p>
      </div>
      <div class="flex justify-between font-bold text-lg">
        <p>Total</p>
        <p>USD ${{ cartTotal.toFixed(2) }}</p>
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

const cartTotal = computed(() => {
  const total = cartStore.cart?.total ?? 0;
  return typeof total === 'number' ? total : parseFloat(total) || 0;
});

const cartSubtotal = computed(() => {
  return cartItemsWithProducts.value.reduce((total, item) => {
    return total + (+item.product.price * item.quantity);
  }, 0);
});



// Ensure products are loaded
onMounted(async () => {
  if (productStore.products.length === 0) {
    await productStore.fetchProducts();
  }
});

const cartItemsWithProducts = computed(() => {
  return (cartStore.cart?.items ?? []).map(cartItem => {
    const product = productStore.products.find(p => p.id === cartItem.id);
    return {
      ...cartItem,
      product: product || { id: cartItem.id, name: 'Loading...', price: '0', image: '', category: { id: 0, name: '' } }
    };
  });
});


</script>