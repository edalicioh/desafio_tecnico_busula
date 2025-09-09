<template>
  <div class="bg-gray-100 min-h-screen py-12">
    <div class="container mx-auto px-4 lg:px-0">
      <div class="max-w-4xl mx-auto">
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900 mb-2">My Orders</h1>
          <p class="text-gray-600">View your order history and track your purchases.</p>
        </div>

        <div v-if="loading" class="text-center py-12">
          <UIcon name="i-lucide-loader-2" class="w-8 h-8 animate-spin mx-auto mb-4" />
          <p class="text-gray-600">Loading your orders...</p>
        </div>

        <div v-else-if="orders.length === 0" class="bg-white rounded-lg shadow-md p-8 text-center">
          <UIcon name="i-lucide-package-x" class="w-16 h-16 text-gray-400 mx-auto mb-4" />
          <h2 class="text-xl font-semibold text-gray-900 mb-2">No orders yet</h2>
          <p class="text-gray-600 mb-6">You haven't placed any orders yet.</p>
          <UButton 
            icon="i-lucide-shopping-cart" 
            size="md" 
            color="primary"
            variant="solid" 
            @click="goToHome">
            Start Shopping
          </UButton>
        </div>

        <div v-else class="space-y-6">
          <div 
            v-for="order in orders" 
            :key="order.id"
            class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow"
          >
            <div class="flex justify-between items-start mb-4">
              <div>
                <h3 class="text-lg font-semibold text-gray-900">Order #{{ order.id }}</h3>
                <p class="text-sm text-gray-600">{{ formatDate(order.created_at) }}</p>
              </div>
              <div class="text-right">
                <UBadge 
                  :color="getStatusColor(order.status)"
                  variant="soft"
                  class="mb-2"
                >
                  {{ order.status }}
                </UBadge>
                <p class="text-lg font-bold text-primary">{{ formatCurrency(order.total) }}</p>
              </div>
            </div>

            <div class="border-t pt-4">
              <h4 class="font-medium text-gray-900 mb-2">Items:</h4>
              <div class="space-y-2">
                <div 
                  v-for="item in order.items" 
                  :key="item.id"
                  class="flex justify-between items-center text-sm"
                >
                  <div class="flex items-center space-x-3">
                    <span class="font-medium">{{ item.product_name }}</span>
                    <UBadge color="gray" variant="soft" size="xs">
                      {{ item.quantity }}x
                    </UBadge>
                  </div>
                  <span class="font-medium">{{ formatCurrency(item.subtotal) }}</span>
                </div>
              </div>
            </div>

            <div class="border-t pt-4 mt-4">
              <div class="flex justify-between items-center text-sm">
                <span class="text-gray-600">Payment Method:</span>
                <span class="font-medium">{{ order.payment_method?.name || 'N/A' }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// Removed authStore dependency for guest access
const orders = ref([])
const loading = ref(true)
const router = useRouter()

const fetchOrders = async () => {
  try {
    const { $api } = useNuxtApp()
    const response = await $api.get('/api/v1/orders')
    orders.value = response.data.data || []
  } catch (error) {
    console.error('Error fetching orders:', error)
    orders.value = []
  } finally {
    loading.value = false
  }
}

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('pt-BR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  }).format(value)
}

const getStatusColor = (status: string) => {
  switch (status) {
    case 'PAID':
      return 'green'
    case 'PENDING':
      return 'yellow'
    case 'CANCELED':
      return 'red'
    default:
      return 'gray'
  }
}

const goToHome = () => {
  router.push('/')
}

onMounted(() => {
  fetchOrders()
})
</script>