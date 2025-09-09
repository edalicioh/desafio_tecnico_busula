import { defineStore } from 'pinia'
import type { PaymentCondition } from '~/types/payment-condition'
import type { PaymentMethod } from '~/types/payment-method'


import { useCartStore } from './cartStore'
import PaymentMethodEnum from '~/enums/payment-method'

export const useCheckoutStore = defineStore('checkoutStore', {

  state: () => ({
    paymentMethods: [] as PaymentMethod[],
    paymentConditions: [] as PaymentCondition[],
    installmentOptions: [] as  {
        value: number;
        label: string;
        conditionId: number;
    }[],
    selectedPaymentMethodId: null as string | null,
    selectedPaymentConditions: null as {
        id: number;
        installments: number | 1;
    } | null,
  }),

  actions: {


    setPaymentMethod(paymentMethods: PaymentMethod[]) {
      this.paymentMethods = paymentMethods
    },


    setPaymentCondition(paymentConditions: PaymentCondition[]) {
      this.paymentConditions = paymentConditions
    },

    setInstallmentOptions(paymentConditions: PaymentCondition[]) {
      const creditCardId = this.paymentMethods.find(pm => pm.name === PaymentMethodEnum.CREDIT_CARD)?.id

      if (!creditCardId) {
        return
      }

      const paymentConditionForCredit = paymentConditions.find(pc => pc.payment_method_id === creditCardId)

      if (!paymentConditionForCredit?.installments) {
        return
      }

      const [startInstallment, lastInstallment] = paymentConditionForCredit.installments
        .split('-')
        .map(i => parseInt(i, 10))

      this.installmentOptions = Array.from({ length: lastInstallment || 0 }, (_, i) => ({
        value: i + 1,
        label: `${i + 1}x`,
        conditionId: paymentConditionForCredit.id
      })).filter(opt => opt.value >= (startInstallment || 0))
    },


    async fetchPaymentMethods() {
      try {
        const { $api } = useNuxtApp()
        const { data } = await $api.get('/api/v1/payment-methods')
        this.setPaymentMethod(data.data)
      } catch (error) {
        console.error('Error fetching payment methods:', error)
      }
    },


    async fetchPaymentConditions() {
      try {
        const { $api } = useNuxtApp()
        const { data } = await $api.get('/api/v1/payment-conditions')
        this.setPaymentCondition(data.data)
        this.setInstallmentOptions(data.data)
      } catch (error) {
        console.error('Error fetching payment conditions:', error)
      }
    },

  }
})