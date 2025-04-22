<template>
  <div class="flex justify-between p-2">
    <div>
      <h3 class="font-medium">{{ group.name }}</h3>
      <p class="text-sm text-gray-500">
        Created: {{ formatDateTime(group.createdAt) }} | Updated: {{ formatDateTime(group.updatedAt) }}
      </p>
    </div>
    <div class="flex items-center space-x-2">
      <button 
        @click="$emit('edit')" 
        class="p-2 text-yellow-500 rounded"
        title="Edit"
      >
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
          <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
        </svg>
      </button>
      <button 
        @click="$emit('delete')" 
        class="p-2 text-red-500 rounded"
        title="Delete"
      >
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M3 6h18"></path>
          <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path>
          <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
          <line x1="10" y1="11" x2="10" y2="17"></line>
          <line x1="14" y1="11" x2="14" y2="17"></line>
        </svg>
      </button>
    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue'
import moment from 'moment'

defineProps({
  group: {
    type: Object,
    required: true
  },
})

defineEmits(['edit', 'delete'])

function formatDateTime(dateTime) {
  return moment(dateTime).format('YYYY-MM-DD, h:mm A')
}
</script>