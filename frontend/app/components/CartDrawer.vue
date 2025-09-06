<template>
  <UDrawer v-model:open="isDrawerOpen" direction="right" class="w-full sm:w-1/3">
    <template #content>
      <div class="p-4 h-full flex flex-col">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-xl font-bold">Your Cart</h2>
          <UButton 
            @click="isDrawerOpen = false" 
            variant="ghost" 
            color="gray"
            icon="i-heroicons-x-mark"
          />
        </div>
        
        <div v-if="cartStore.cartItems.length === 0" class="text-center py-8 flex-1 flex flex-col justify-center">
          <UIcon name="i-heroicons-shopping-cart" class="mx-auto h-12 w-12 text-gray-400" />
          <p class="mt-2 text-sm text-gray-500">Your cart is empty</p>
        </div>
        
        <div v-else class="flex-1 overflow-y-auto">
          <div 
            v-for="item in cartItemsWithProducts" 
            :key="item.id"
            class="justify-between mb-6 rounded-lg bg-white p-6 shadow-md sm:flex sm:justify-start"
          >
            <img 
              :src="item.product.image" 
              :alt="item.product.name" 
              class="w-full rounded-lg sm:w-40" 
            />
            <div class="sm:ml-4 sm:flex sm:w-full sm:justify-between">
              <div class="mt-5 sm:mt-0">
                <h2 class="text-lg font-bold text-gray-900">{{ item.product.name }}</h2>
                <p class="mt-1 text-xs text-gray-700">{{ item.product.category.name }}</p>
              </div>
              <div class="mt-4 flex justify-between sm:space-y-6 sm:mt-0 sm:block sm:space-x-6">
                <div class="flex items-center border-gray-100">
                  <span 
                    class="cursor-pointer rounded-l bg-gray-100 py-1 px-3.5 duration-100 hover:bg-blue-500 hover:text-blue-50"
                    @click="decreaseQuantity(item.id)"
                  > - </span>
                  <input
                    class="h-8 w-8 border bg-white text-center text-xs outline-none"
                    type="number"
                    :value="item.quantity"
                    min="1"
                    readonly
                  />
                  <span 
                    class="cursor-pointer rounded-r bg-gray-100 py-1 px-3 duration-100 hover:bg-blue-500 hover:text-blue-50"
                    @click="increaseQuantity(item.id)"
                  > + </span>
                </div>
                <div class="flex items-center space-x-4">
                  <p class="text-sm">${{ (parseFloat(item.product.price) * item.quantity).toFixed(2) }}</p>
                  <svg 
                    xmlns="http://www.w3.org/2000/svg" 
                    fill="none" 
                    viewBox="0 0 24 24" 
                    stroke-width="1.5" 
                    stroke="currentColor" 
                    class="h-5 w-5 cursor-pointer duration-150 hover:text-red-500"
                    @click="removeItem(item.id)"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="border-t pt-4 mt-4">
          <div class="flex justify-between text-lg font-bold mb-4">
            <span>Total:</span>
            <span>${{ cartTotal.toFixed(2) }}</span>
          </div>
          <UButton 
            class="w-full" 
            color="primary"
            @click="checkout"
          >
            Proceed to Checkout
          </UButton>
        </div>
      </div>
    </template>
  </UDrawer>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useCartStore } from '../store/cartStore'
import { useProductStore } from '../store/productStore'
import { useRouter } from '#app'

const cartStore = useCartStore()
const productStore = useProductStore()
const router = useRouter()
const isDrawerOpen = ref(false)

onMounted(async () => {
  if (productStore.products.length === 0) {
    await productStore.fetchProducts()
  }
})

const cartItemsWithProducts = computed(() => {
  return cartStore.cartItems.map(cartItem => {
    const product = productStore.products.find(p => p.id === cartItem.id);
    return {
      ...cartItem,
      product: product || { id: cartItem.id, name: 'Loading...', price: '0', image: '', category: { id: 0, name: '' } }
    };
  });
});

const cartTotal = computed(() => {
  return cartItemsWithProducts.value.reduce((total, item) => {
    const price = parseFloat(item.product.price) || 0;
    return total + (price * item.quantity);
  }, 0);
});

const increaseQuantity = (productId: number) => {
  const item = cartItemsWithProducts.value.find(item => item.id === productId);
  if (item) {
    cartStore.updateItemQuantity(productId, item.quantity + 1);
  }
};

const decreaseQuantity = (productId: number) => {
  const item = cartItemsWithProducts.value.find(item => item.id === productId);
  if (item && item.quantity > 1) {
    cartStore.updateItemQuantity(productId, item.quantity - 1);
  } else {
    cartStore.removeItemFromCart(productId);
  }
};

const removeItem = (productId: number) => {
  cartStore.removeItemFromCart(productId);
};

const checkout = () => {
  isDrawerOpen.value = false;
  router.push('/checkout');
}

defineExpose({
  open: () => {
    isDrawerOpen.value = true
  }
})
</script>