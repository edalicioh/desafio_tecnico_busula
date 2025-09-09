<template>
  <div class="flex flex-col gap-2">
    <USelect class="w-full" v-model="installments" :items="installmentOptions" icon="i-lucide-layers"
      @update:model-value="onInstallmentChange" />


    <UInput placeholder="John Smith" />

    <UInput v-maska="'#### #### #### ####'" placeholder="4242 4242 4242 4242" icon="i-lucide-credit-card" />

    <div class="flex items-center gap-2">
      <UInput v-maska="'##/##'" placeholder="MM/YY" icon="i-lucide-calendar" />
      <UInput v-maska="'###'" placeholder="CVC" />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { vMaska } from 'maska/vue'
import { useCheckoutStore } from '~/store/checkoutStore'
import { useCartStore } from '~/store/cartStore'

const store = useCheckoutStore()
const cartStore = useCartStore()

const installmentOptions = computed(() => store.installmentOptions)

const installments = ref(installmentOptions.value[0] ?? null)

onMounted(() => {
   cartStore.recalculateTotal(installments.value?.conditionId!, installments.value?.value!)
})

const onInstallmentChange = (value: number | null) => {
  if (value !== null && value !== undefined) {
    const selectedOption = installmentOptions.value.find(option => option.value === value)
    if (selectedOption) {
      store.selectedPaymentConditions = { id:selectedOption.conditionId, installments : selectedOption.value}
      cartStore.recalculateTotal(selectedOption.conditionId, selectedOption.value)
    }
  }
}

</script>
