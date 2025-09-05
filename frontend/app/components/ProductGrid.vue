<template>
  <section class="py-10 bg-gray-100">
    <UContainer>
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6">
        <ProductCard v-for="product in products" :key="product.id" :product="product" @add-to-cart="addToCart" />
      </div>
      <div class="mt-10 flex justify-center">

        <UPagination v-model:page="page" :total="100" />
      </div>
    </UContainer>
  </section>
</template>

<script setup lang="ts">
import type { Product } from '../types/product'
import ProductCard from './ProductCard.vue'

const page = ref(1)

const props = defineProps({
  products: {
    type: Array as () => Product[],
    required: true,
  },
})

const emit = defineEmits(['add-to-cart'])

const addToCart = (product: Product) => {
  emit('add-to-cart', product)
}
</script>