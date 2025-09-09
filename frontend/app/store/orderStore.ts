import { defineStore } from 'pinia';
import type { Order } from '~/types/order';

interface OrderState {
  orders: Order[];
  loading: boolean;
  error: string | null;
}

export const useOrderStore = defineStore('order', {
  state: (): OrderState => ({
    orders: [],
    loading: false,
    error: null,
  }),
  actions: {

    async fetchOrders() {
      this.loading = true;
      this.error = null;
      try {
        const { data } = await useFetch<Order[]>('/api/v1/orders');
        if (data.value) {
          this.orders = data.value;
        }
      } catch (error) {
        this.error = 'Failed to fetch orders';
      } finally {
        this.loading = false;
      }
    },

    async createOrder(order: Order) {
      const { $api } = useNuxtApp()
      try {
        const {data} = await $api.post('/api/v1/orders', order)
        return data.data
      } catch (error) {
        return false
      }
    },
  },
});