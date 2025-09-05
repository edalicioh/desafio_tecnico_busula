    <template >
      <div class="bg-white rounded-lg shadow-sm hover:shadow-md duration-200">

        <div class="relative overflow-hidden rounded-xl h-[calc(50%-1.5rem)]">
          <img class="object-cover aspect-square w-full" :src="product.image" :alt="product.name" />
        </div>

        <div class="mt-1 p-2 h-[40%]">
          <h2 class="text-slate-700">{{ product.name }}</h2>
          <p class="mt-1 text-sm text-slate-400">{{ product.category }}</p>
          <div class="mt-3 flex items-end justify-between">
            <UBadge color="blue">{{ product.price }}</UBadge>
          </div>
        </div>

        <div class="mt-3 mb-4 px-2 ">
          <UButton color="primary" class="w-full flex items-center justify-center" @click="addToCart">
            <UIcon name="i-heroicons-shopping-cart" class="h-5 w-5" />
            <span class="ml-2 hidden md:inline">Add to cart</span>
          </UButton>
          <!-- <div
            class="flex items-center justify-center space-x-1.5 rounded-lg bg-blue-500 px-4 py-1.5 text-white duration-100 hover:bg-blue-600 p-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor" class="h-4 w-4">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
            </svg>

            <button class="text-sm" @click="addToCart">Add to cart</button>
          </div> -->
        </div>
      </div>
    </template>

<script setup lang="ts">
import type { Product } from '../types/product'

const props = defineProps({
  product: {
    type: Object as () => Product,
    required: true,
  },
})

const emit = defineEmits(['add-to-cart'])

const addToCart = () => {
  emit('add-to-cart', props.product)
}
</script>