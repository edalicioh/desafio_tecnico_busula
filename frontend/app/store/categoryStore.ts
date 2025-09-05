import type { Category } from "~/types/category";


export const useCategoryStore = defineStore('categoryStore', {

  state: () => ({
    categories: [] as Category[],
  }),

  actions: {
    setCategories(categories: Category[]) {
      this.categories = categories;
    },


    async fetchCategories() {
      const { $api } = useNuxtApp();
      try {
        const response = await $api.get('/api/v1/categories');
        const categories = response.data.data;
        console.log(categories);
        this.setCategories([{id: 0 , name: 'All'}, ...categories]);
      } catch (error) {
        console.error('Error fetching categories:', error);
      }
    }
  },

  getters: {
    getCategories(state) {
      return state.categories;
    },
  },



});
