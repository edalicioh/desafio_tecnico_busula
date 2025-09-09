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

   

    async createOrder(order: { cart_id: number, payment_condition_id: number,installments: number  }) {
      const { $api } = useNuxtApp()
      try {
        console.log(order)
        const {data} = await $api.post('/api/v1/orders', order)
        useCookie('session_id').value = null
        return data.data
      } catch (error) {
        return false
      }
    },

  },
});