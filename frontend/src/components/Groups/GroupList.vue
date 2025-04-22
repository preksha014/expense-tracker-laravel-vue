<template>
  <ul class="divide-y divide-gray-200">
    <li 
      v-for="(group, index) in groups" 
      :key="index" 
      class="py-3"
    >
      <group-item 
        :group="group"
        @edit="$emit('edit', index)" 
        @delete="$emit('delete', index)" 
      />
    </li>
  </ul>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue'
import GroupItem from './GroupItem.vue'

defineProps({
  groups: {
    type: Array,
    required: true
  },
})

defineEmits(['edit', 'delete'])
</script>
