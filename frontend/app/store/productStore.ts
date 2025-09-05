import type { Product } from "~/types/product";

export const  useProductStore = defineStore('productStore', {
  
  state: () => ({
    products: [] as Product[],
  }),
  
  actions: {
    setProducts(products: Product[]) {
      this.products = products;
    },

    async fetchProducts() {
      const { $api } = useNuxtApp();
      try {
        const response = await $api.get('/api/v1/products');
        console.log(response.data.data);
        this.setProducts(response.data.data);
      } catch (error) {
        console.error('Error fetching products:', error);
      }
    },
  },
  
});   
