<template>
  <UCard v-if="paymentMethods.length">
    <h2 class="text-xl font-semibold mb-4">Payment</h2>
    
    <div class="flex gap-2">
      <UButton
        v-for="method in paymentMethods"
        :key="method.id"
        :color="selectedMethod === method.name ? 'primary' : 'gray'"
        variant="outline"
        @click="selectPaymentMethod(method.name)"
        class="flex items-center gap-2"
      >
        <UIcon :name="method.icon" />
        {{ method.label }}
      </UButton>
    </div>
    
    <!-- Display only the selected payment method -->
    <div v-if="selectedMethod === PaymentMethodEnum.CREDIT_CARD" class="pt-4" >
      <h3 class="text-lg font-medium mb-3">Credit Card</h3>
      <CreditCardPayment />
    </div>
    
    <div v-else-if="selectedMethod === PaymentMethodEnum.DEBIT_CARD" class="pt-4">
      <h3 class="text-lg font-medium mb-3">Debit Card</h3>
      <DebitCardPayment />
    </div>
    
    <div v-else-if="selectedMethod === PaymentMethodEnum.PIX" class="pt-4">
      <h3 class="text-lg font-medium mb-3">PIX</h3>
      <PIXPayment />
    </div>
  </UCard>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useCheckoutStore } from '../store/checkoutStore'
import { useCartStore } from '../store/cartStore'
import CreditCardPayment from './CreditCardPayment.vue'
import DebitCardPayment from './DebitCardPayment.vue'
import PIXPayment from './PIXPayment.vue'
import PaymentMethodEnum from '~/enums/payment-method'

const store = useCheckoutStore();
const cartStore = useCartStore();

const setIcon = (name: string) => {
  switch (name) {
    case PaymentMethodEnum.CREDIT_CARD:
      return 'i-lucide-credit-card'
    case PaymentMethodEnum.DEBIT_CARD:
      return 'i-lucide-banknote'
    case PaymentMethodEnum.PIX:
      return 'i-lucide-zap'
    default:
      return 'i-lucide-help-circle'
  }
}

const paymentMethods = computed(() =>
  store.paymentMethods.map(i => {
    return {
      ...i,
      label: i.name,
      icon: setIcon(i.name)
    }
  })
)

const selectedMethod = ref(PaymentMethodEnum.CREDIT_CARD as string)

const selectPaymentMethod = (methodId: string) => {
  console.log(methodId)
  selectedMethod.value = methodId
  const method = store.paymentMethods.filter(pm => pm.name === methodId)[0]
  const condition = store.paymentConditions.filter(pc => pc.payment_method_id == method?.id )[0]
  cartStore.recalculateTotal(condition?.id! , Number(condition?.installments.split('-')[0] ?? 1) )
}

onMounted(() => {
  selectPaymentMethod(selectedMethod.value)
})
</script>