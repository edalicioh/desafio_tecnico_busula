<template>
  <UButton icon="i-lucide-lock" size="md" color="primary" variant="solid"
    class="w-full font-bold py-3 rounded-md mt-6 justify-center" :loading="isLoading"
    :disabled="isLoading || !canCheckout" @click="handleCheckout">
    {{ isLoading ? 'Processing...' : 'PAY NOW' }}
  </UButton>
</template>

<script setup lang="ts">
import type { Order } from '~/types/order'
import { useCartStore } from '../store/cartStore'
import { useCheckoutStore } from '../store/checkoutStore'
import { useOrderStore } from '../store/orderStore'

const cartStore = useCartStore()
const checkoutStore = useCheckoutStore()
const orderStore = useOrderStore()
const isLoading = computed(() => orderStore.loading)
const router = useRouter()
const toast = useToast()

const canCheckout = computed(() => {
  return cartStore.cart &&
    cartStore.cart.items &&
    cartStore.cart.items.length > 0 &&
    cartStore.cart.total !== null &&
    cartStore.cart.total > 0
})

const handleCheckout = async () => {
  if (!canCheckout.value || !cartStore.cart) {
    return
  }

  if (!checkoutStore.selectedPaymentMethodId) {
    toast.add({
      title: 'Condition',
      description: 'Payment condition not found',
      color: 'error'
    })
    return
  }

  if(!checkoutStore.selectedPaymentConditions?.id){
        toast.add({
      title: 'Condition',
      description: 'Payment condition not found',
      color: 'error'
    })
    return
  }

  if (cartStore.cart?.id) {


    const res = await orderStore.createOrder({
      cart_id: cartStore.cart.id,
      payment_condition_id: checkoutStore.selectedPaymentConditions?.id!,
      installments: checkoutStore.selectedPaymentConditions?.installments || 1
    })

    if (res) {
      toast.add({
        title: 'Error',
        description: 'Error create order',
        color: 'error'
      })
    }

    await router.push({
      path: '/order-confirmation',
      query: {
        orderId: res.id,
        total: res.total
      }
    })
  }


}
</script>