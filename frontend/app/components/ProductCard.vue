    <template >
      <div class="bg-white rounded-lg shadow-sm hover:shadow-md duration-200">

        <div class="relative overflow-hidden rounded-xl h-[calc(50%-1.5rem)]">
          <img class="object-cover aspect-square w-full" :src="product.image" :alt="product.name" />
        </div>

        <div class="mt-1 p-2 h-[40%]">
          <h2 class="text-slate-700">{{ product.name }}</h2>
          <p class="mt-1 text-sm text-slate-400">{{ product.category.name }}</p>
          <div class="mt-3 flex items-end justify-between">
            <UBadge color="blue">{{ product.price }}</UBadge>
          </div>
        </div>

        <div class="mt-3 mb-4 px-2 ">
          <UButton color="primary" class="w-full flex items-center justify-center" @click="addToCart">
            <UIcon name="i-heroicons-shopping-cart" class="h-5 w-5" />
            <span class="ml-2 hidden md:inline">Add to cart</span>
          </UButton>
        </div>
      </div>
    </template>

<script setup lang="ts">
import { useCartStore } from '~/store/cartStore'
import type { Product } from '../types/product'

const props = defineProps({
  product: {
    type: Object as () => Product,
    required: true,
  },
})

const cartStore = useCartStore()

const addToCart = () => {
  console.log(props)
  cartStore.addItemToCart(props.product)
}
</script>