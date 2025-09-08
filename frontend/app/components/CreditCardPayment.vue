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
import { ref } from 'vue'
import { vMaska } from 'maska/vue'
import { useCheckoutStore } from '~/store/checkoutStore'
import { useCartStore } from '~/store/cartStore'

const store = useCheckoutStore()
const cartStore = useCartStore()

const installments = ref<number | null>(null)

const installmentOptions = store.installmentOptions

const onInstallmentChange = (value: number | null) => {
  if (value) {
    const selectedOption = installmentOptions.find(option => option.value === value)
    if (selectedOption) {
      cartStore.recalculateTotal(selectedOption.conditionId, selectedOption.value)
    }
  }
}

</script>
