<template>
  <UDrawer v-model:open="isDrawerOpen" direction="right" class="w-full sm:w-1/3 bg-elevated">
    <template #content>
      <div class="p-4 h-full flex flex-col">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-xl font-bold text-default">Your Cart</h2>
          <UButton @click="isDrawerOpen = false" variant="ghost" color="neutral" icon="i-heroicons-x-mark" />
        </div>

        <div v-if="cartStore.cartItems.length === 0" class="text-center py-8 flex-1 flex flex-col justify-center">
          <UIcon name="i-heroicons-shopping-cart" class="mx-auto h-6 w-6 text-muted" />
          <p class="mt-2 text-sm text-muted">Your cart is empty</p>
        </div>

        <div v-else class="flex-1 overflow-y-auto">
          <UCard v-for="item in cartItemsWithProducts" :key="item.id" class="mb-6"
            :ui="{ body: { padding: 'p-4' } }">
            <div class="flex sm:w-full sm:justify-between flex-col">

              <UTooltip :text="item.product.name" :popper="{ placement: 'top' }">
                <h2
                  class="text-lg font-bold text-default truncate whitespace-nowrap overflow-hidden max-w-[200px] cursor-help"
                >
                  {{ item.product.name }}
                </h2>
              </UTooltip>

              <div class="flex  justify-between mt-2 items-center">
                  <p class="text-sm text-default mt-2 sm:mt-0">${{ (parseFloat(item.product.price) *
                    item.quantity).toFixed(2) }}
                  </p>
                <div class="flex items-center mt-4 sm:mt-0 max-w-1/2 gap-0.5">
                  <UButton icon="i-heroicons-minus" size="md" color="primary" variant="solid"
                    @click="decreaseQuantity(item.id)" :ui="{ rounded: 'rounded-l-full' }" />
                  <UInput :model-value="item.quantity" type="number" min="1" readonly
                    input-class="h-8 w-  text-center text-xs"
                    :ui="{ wrapper: 'relative', base: 'relative block w-full disabled:cursor-not-allowed disabled:opacity-75 focus:outline-none border-0', input: 'block w-full rounded-md border-0 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-700 focus:ring-2 focus:ring-inset focus:ring-primary-500 dark:focus:ring-primary-400 text-sm' }" />
                  <UButton icon="i-heroicons-plus" size="md" color="primary" variant="solid"
                    @click="increaseQuantity(item.id)" :ui="{ rounded: 'rounded-r-full' }" />
                </div>

              </div>
            </div>
          </UCard>
        </div>

        <div class="border-t border-muted pt-4 mt-4">
          <div class="flex justify-between text-lg font-bold mb-4 text-default">
            <span>Total:</span>
            <span>${{ cartTotal.toFixed(2) }}</span>
          </div>
          <UButton class="w-full flex items-center justify-center" color="primary" @click="checkout">
            <UIcon name="i-heroicons-shopping-cart" class="h-5 w-5" />
            <span class="ml-2">Proceed to Checkout</span>
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