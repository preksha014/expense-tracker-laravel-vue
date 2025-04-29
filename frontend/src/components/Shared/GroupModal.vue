<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded shadow-lg max-w-md w-full">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">{{ isEditing ? 'Edit Group' : 'Add Group' }}</h2>
        <button @click="$emit('close')" class="text-gray-500 hover:text-gray-700">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      
      <GroupForm 
        :group="group"
        @save="handleSave" 
        @close="$emit('close')" 
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { defineProps, defineEmits, computed } from 'vue'
import GroupForm from '../Groups/GroupForm.vue'

const props = defineProps({
  group: {
    type: Object,
    default: () => ({
      name: ''
    })
  }
})

const emit = defineEmits(['save', 'close'])

// Check if we're editing an existing group
const isEditing = computed(() => {
  return props.group.id !== undefined || 
         (props.group.name && props.group.name.trim() !== '')
})

function handleSave(groupData) {
  emit('save', groupData)
}
</script>