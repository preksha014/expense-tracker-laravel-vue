<template>
    <div class="mb-4">
      <label :for="id" class="block text-sm font-medium text-gray-700 mb-1">{{ label }}</label>
      <input
        :id="id"
        v-model="model"
        :type="type"
        :min="min"
        :step="step"
        :placeholder="placeholder"
        class="w-full p-2 border border-gray-300 rounded"
      />
      <span v-if="error" class="text-red-500 text-sm mt-1">
        {{ error }}
      </span>
    </div>
  </template>
  
  <script setup>
  import { defineProps, defineModel } from 'vue'
  
  defineProps({
    id: {
      type: String,
      required: true
    },
    label: {
      type: String,
      required: true
    },
    type: {
      type: String,
      default: 'text'
    },
    placeholder: {
      type: String,
      default: ''
    },
    min: {
      type: [String, Number],
      default: null
    },
    step: {
      type: [String, Number],
      default: null
    },
    error: {
      type: String,
      default: ''
    }
  })
  
  // Use defineModel for two-way binding
  const model = defineModel()
  </script>