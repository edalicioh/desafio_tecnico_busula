<template>
  <div class="pt-32 bg-white">
    <UContainer class="flex justify-center">
      <h1 class="text-center text-2xl font-bold text-gray-800">{{ title }}</h1>
    </UContainer>
  </div>
</template>

<script setup lang="ts">
defineProps({
  title: {
    type: String,
    required: true,
  },
})
</script>