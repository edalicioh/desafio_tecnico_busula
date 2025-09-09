<template>
  <header class="fixed top-0 left-0 z-20 w-full border-b border-gray-200 bg-white py-2.5 px-6 sm:px-4">
    <UContainer class="flex max-w-6xl flex-wrap items-center justify-between">
      <NuxtLink to="/" class="flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mr-3 h-6 text-blue-500 sm:h-9">
          <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
        </svg>
        <span class="self-center whitespace-nowrap text-xl font-semibold">Bussola Store</span>
      </NuxtLink>
      <div class="mt-2 sm:mt-0 sm:flex md:order-2">
        <UButton color="primary" variant="outline" class="md:ml-3" @click="openCartDrawer">
          <UIcon name="i-heroicons-shopping-cart" class="h-5 w-5" />
          <span class="ml-2 hidden md:inline">Cart</span>
        </UButton>
      </div>
    </UContainer>
  </header>
  <CartDrawer ref="cartDrawer" />
</template>

<script setup lang="ts">
import { ref } from 'vue'
import CartDrawer from './CartDrawer.vue'

const isMenuOpen = ref(false)
const cartDrawer = ref<InstanceType<typeof CartDrawer> | null>(null)

const openCartDrawer = () => {
  if (cartDrawer.value) {
    cartDrawer.value.open()
  }
}
</script>