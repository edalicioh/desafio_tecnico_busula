<template>
  <div class="bg-gray-100 min-h-screen py-12">
    <div class="container mx-auto px-4 lg:px-0">
      <div class="lg:flex lg:space-x-12">
        <div class="lg:w-1/2">
          <CheckoutBillingAddress class="mb-6" />
          <CheckoutPayment />
        </div>

        <div class="lg:w-1/2 mt-8 lg:mt-0">
          <CheckoutCartSummary />
          <CheckoutPayButton class="mt-6" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useCartStore } from '../store/cartStore'
import { useProductStore } from '../store/productStore'
import { useCheckoutStore } from '../store/checkoutStore'
import CheckoutBillingAddress from '../components/CheckoutBillingAddress.vue'
import CheckoutPayment from '../components/CheckoutPayment.vue'
import CheckoutCartSummary from '../components/CheckoutCartSummary.vue'
import CheckoutPayButton from '../components/CheckoutPayButton.vue'

const cartStore = useCartStore()
const productStore = useProductStore()
const checkoutStore = useCheckoutStore()

useAsyncData('checkout-data', async () => {
  await checkoutStore.fetchPaymentMethods()
  await checkoutStore.fetchPaymentConditions()
  await cartStore.getCart()

  if (productStore.products.length === 0) {
    await productStore.fetchProducts()
  }
})
</script>