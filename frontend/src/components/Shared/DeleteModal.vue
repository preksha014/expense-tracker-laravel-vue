<template>
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white p-6 rounded shadow-lg max-w-md w-full">
        <h2 class="text-xl font-bold mb-4">{{ title }}</h2>
        
        <p class="mb-6">{{ message }}</p>
        
        <div class="flex justify-end space-x-3">
          <button 
            @click="$emit('cancel')" 
            class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-100"
          >
            Cancel
          </button>
          <button 
            @click="$emit('confirm')" 
            class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600"
          >
            Delete
          </button>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { defineProps, defineEmits } from 'vue'
  
  defineProps({
    title: {
      type: String,
      default: 'Confirm Deletion'
    },
    message: {
      type: String,
      default: 'Are you sure you want to delete this item? This action cannot be undone.'
    }
  })
  
  defineEmits(['confirm', 'cancel'])
  </script>