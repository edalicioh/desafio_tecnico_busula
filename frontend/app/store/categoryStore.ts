

export const useCategoryStore = defineStore('categoryStore', {

  state: () => ({
    categories: [] as string[],
  }),

  actions: {
    setCategories(categories: string[]) {
      this.categories = categories;
    },


    async fetchCategories() {
      const { $api } = useNuxtApp();
      try {
        const response = await $api.get('/api/v1/categories');
        const categories = response.data.data.map((category: any) => category.name);
        console.log(categories);
        this.setCategories(['All', ...categories]);
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
