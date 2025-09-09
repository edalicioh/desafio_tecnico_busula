<template>
  <div class="bg-gray-100 min-h-screen py-12">
    <div class="container mx-auto px-4 lg:px-0">
      <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-8 text-center">
          <div class="mb-6">
            <UIcon name="i-lucide-check-circle" class="w-16 h-16 text-green-500 mx-auto mb-4" />
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Order Confirmed!</h1>
            <p class="text-gray-600">Thank you for your purchase. Your order has been successfully placed.</p>
          </div>

          <div class="bg-gray-50 rounded-lg p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
              <span class="text-gray-600">Order ID:</span>
              <span class="font-semibold">#{{ orderId }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-gray-600">Total Amount:</span>
              <span class="font-bold text-xl text-primary">{{ formatCurrency(total) }}</span>
            </div>
          </div>

          <div class="space-y-4">
            <UButton 
              icon="i-lucide-home" 
              size="md" 
              color="primary"
              variant="solid" 
              class="w-full"
              @click="goToHome">
              Continue Shopping
            </UButton>
            
            <UButton 
              icon="i-lucide-package" 
              size="md" 
              color="gray"
              variant="outline" 
              class="w-full"
              @click="viewOrders">
              View My Orders
            </UButton>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const route = useRoute()
const router = useRouter()

const orderId = computed(() => route.query.orderId as string || 'N/A')
const total = computed(() => parseFloat(route.query.total as string) || 0)

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  }).format(value)
}

const goToHome = () => {
  router.push('/')
}

const viewOrders = () => {
  router.push('/orders')
}
</script>