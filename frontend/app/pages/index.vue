<template>
  <div>
    <Header />
    <ProductTitle :title="pageTitle" />
    <CategoryTabs :categories="categories" @category-change="handleCategoryChange" />
    <ProductGrid :products="filteredProducts" @add-to-cart="handleAddToCart" />
    <Footer />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import Header from '../components/Header.vue'
import ProductTitle from '../components/ProductTitle.vue'
import CategoryTabs from '../components/CategoryTabs.vue'
import ProductGrid from '../components/ProductGrid.vue'
import Footer from '../components/Footer.vue'
import type { Product } from '../types/product'
import { useCategoryStore } from '../store/categoryStore'
import { useProductStore } from '../store/productStore'

const pageTitle = ref('All Products')

const categoryStore = useCategoryStore()
const productStore = useProductStore()

const categories = computed(() => categoryStore.categories)
const products = computed(() => productStore.products)

onMounted(async () => {
  await categoryStore.fetchCategories()
  await productStore.fetchProducts()
})

const selectedCategory = ref('All')

const filteredProducts = computed(() => {
  if (selectedCategory.value === 'All') {
    return products.value
  }
  return products.value.filter((product: Product) => product.category === selectedCategory.value)
})

const handleCategoryChange = (category: { label: string }) => {
  selectedCategory.value = category.label
}

const handleAddToCart = (product: Product) => {
  console.log('Added to cart:', product)
}
</script>