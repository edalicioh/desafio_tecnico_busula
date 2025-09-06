import { v4 as uuidv4 } from 'uuid';
import type { Cart } from "~/types/cart";
import type { CartItem } from "~/types/cart-item";
import type { Product } from "~/types/product";


export const useCartStore = defineStore('cartStore', {


    state: () => ({
        cart: null as Cart | null,
        cartItems: [] as CartItem[],
    }),



    actions: {
        initCart: () => ({
            id: null,
            status: 'OPEN',
            userId: null,
            totol: null,
            items: [],
        } as Cart),

        setCart(cart: Cart) {
            this.cart = cart;
            this.cartItems = cart.items;
        },



        async getCart() {
            const { $api } = useNuxtApp();
            const sessionCookie = useCookie<string>('session_id');
            let sessionId = sessionCookie.value;

            const initCart = this.initCart();
            initCart.userId = sessionId

            if (!sessionId) {
                sessionId = uuidv4();
                sessionCookie.value = sessionId;
                this.setCart(initCart);
                return;
            }

            try {
                const response = await $api.get(`/api/v1/cart/${sessionId}`);

                if (response.status === 404) {
                    sessionId = uuidv4();
                    sessionCookie.value = sessionId;
                    this.setCart(initCart);
                    return;
                }

                this.setCart(response.data.data);
            } catch (error) {
                console.error('Erro ao buscar carrinho:', error);
                this.setCart(initCart);
            }



        },


        async addItemToCart(item: Product) {
            const { $api } = useNuxtApp();

            const existingItem = this.cartItems.find(cartItem => cartItem.id === item.id);

            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                this.cartItems.push({ id: item.id, quantity: 1, subtotal: null });
            }
            if (this.cart) {
                this.cart.items = this.cartItems;
            }

            console.log('Cart Items:', this.cartItems);

            const response = await $api.post('/api/v1/cart', this.cart)
            this.setCart(response.data.data);

        },
        removeItemFromCart(productId: number) {
            this.cartItems = this.cartItems.filter(item => item.id !== productId);
        },
        clearCart() {
            this.cart = null;
            this.cartItems = [];
        },
        async updateItemQuantity(productId: number, quantity: number) {
            const { $api } = useNuxtApp();

            const item = this.cartItems.find(item => item.id === productId);
            
            if (item) {
                item.quantity = quantity;
            }

             const response = await $api.post('/api/v1/cart', this.cart)
            this.setCart(response.data.data);
        }
    }

});