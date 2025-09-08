import { v4 as uuidv4 } from 'uuid'
import type { Cart } from '~/types/cart'
import type { CartItem } from '~/types/cart-item'
import type { Product } from '~/types/product'

export const useCartStore = defineStore('cartStore', {
  state: () => ({
    cart: null as Cart | null,
    cartItems: [] as CartItem[],
  }),

  actions: {
    createEmptyCart(sessionId?: string): Cart {
      return {
        id: null,
        status: 'OPEN',
        sessionId: sessionId || null,
        userId: sessionId || null,
        total: 0,
        items: [],
      }
    },

    setCart(cart: Cart) {
      this.cart = cart
      this.cartItems = cart.items || []
    },

    async getCart() {
      const { $api } = useNuxtApp()
      const sessionCookie = useCookie<string>('session_id')
      let sessionId = sessionCookie.value

      if (!sessionId) {
        sessionId = uuidv4()
        sessionCookie.value = sessionId
        this.setCart(this.createEmptyCart(sessionId))
        return
      }

      try {
        const response = await $api.get(`/api/v1/cart/${sessionId}`)

        if (response.status === 404) {
          sessionId = uuidv4()
          sessionCookie.value = sessionId
          this.setCart(this.createEmptyCart(sessionId))
          return
        }

        this.setCart(response.data.data)
      } catch (error) {
        console.error('Erro ao buscar carrinho:', error)
        this.setCart(this.createEmptyCart(sessionId))
      }
    },

    async addItemToCart(product: Product) {
      const { $api } = useNuxtApp()

      const existingItem = this.cartItems.find(
        (cartItem) => cartItem.id === product.id
      )

      if (existingItem) {
        existingItem.quantity += 1
      } else {
        this.cartItems.push({
          id: product.id,
          quantity: 1,
          subtotal: 0,
        })
      }

      if (this.cart) {
        this.cart.items = this.cartItems
      }

      try {
        const response = await $api.post('/api/v1/cart', this.cart)
        this.setCart(response.data.data)
      } catch (error) {
        console.error('Erro ao salvar item no carrinho:', error)
      }
    },

    async removeItemFromCart(productId: number) {
      const { $api } = useNuxtApp()

      this.cartItems = this.cartItems.filter((item) => item.id !== productId)
      if (this.cart) {
        this.cart.items = this.cartItems
      }

      try {
        const response = await $api.post('/api/v1/cart', this.cart)
        this.setCart(response.data.data)
      } catch (error) {
        console.error('Erro ao remover item do carrinho:', error)
      }
    },

    clearCart() {
      this.cart = null
      this.cartItems = []
      useCookie('session_id').value = null
    },

    async updateItemQuantity(productId: number, quantity: number) {
      const { $api } = useNuxtApp()

      if (quantity === 0) {
        await this.removeItemFromCart(productId)
        return
      }

      const item = this.cartItems.find((item) => item.id === productId)
      if (item) {
        item.quantity = quantity
      }

      if (this.cart) {
        this.cart.items = this.cartItems
      }

      try {
        const response = await $api.post('/api/v1/cart', this.cart)
        this.setCart(response.data.data)
      } catch (error) {
        console.error('Erro ao atualizar quantidade:', error)
      }
    },
    async recalculateTotal(conditionId: number, installments: number) {
      if (!this.cart) return

      try {
        const { $api } = useNuxtApp()
        const response = await $api.post('/api/v1/cart/recalculate', {
          sessionId: this.cart.sessionId,
          conditionId,
          installments,
        })
        this.setCart(response.data.data)
      } catch (error) {
        console.error('Erro ao recalcular o total do carrinho:', error)
      }
    },
  },

})
