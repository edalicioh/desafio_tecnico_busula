<template>
  <div class="w-full bg-white text-gray-800 py-2">
    <div
      class="flex items-center gap-4 overflow-x-auto whitespace-nowrap scrollbar-hide snap-x snap-mandatory px-4"
    >
      <UTabs :items="categories" @change="onCategoryChange">
        <template #default="{ item, selected }">
          <div
            class="flex items-center gap-2 truncate px-3 py-1 rounded-lg cursor-pointer snap-start transition-colors"
            :class="selected
              ? 'bg-primary-500 text-white'
              : 'hover:bg-gray-100 text-gray-700'"
          >
            <span class="truncate max-w-[120px]">{{ item.name }}</span>
          </div>
        </template>
      </UTabs>
    </div>
  </div>
</template>


<script setup lang="ts">
import type { Category } from '../types/category'
const props = defineProps({
  categories: {
    type: Array as () => Category[],
    required: true,
  },
})

const emit = defineEmits(['category-change'])

const onCategoryChange = (index: number) => {
  emit('category-change', props.categories[index])
}
</script>