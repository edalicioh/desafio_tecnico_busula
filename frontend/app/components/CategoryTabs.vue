<template>
  <div class="flex flex-wrap items-center overflow-x-auto overflow-y-hidden py-10 justify-center bg-white text-gray-800">
    <UTabs :items="categories" @change="onCategoryChange">
      <template #default="{ item, index, selected }">
        <div class="flex items-center gap-2 relative truncate">
          <span class="truncate">{{ item.name }}</span>
          <span v-if="selected" class="absolute -right-4 w-2 h-2 rounded-full bg-primary-500 dark:bg-primary-400" />
        </div>
      </template>
    </UTabs>
  </div>
</template>

<script setup lang="ts">
const props = defineProps({
  categories: {
    type: Array as () => Array<{ id: number; name: string }>,
    required: true,
  },
})

const emit = defineEmits(['category-change'])

const onCategoryChange = (index: number) => {
  emit('category-change', props.categories[index])
}
</script>