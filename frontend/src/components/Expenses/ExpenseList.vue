<template>
  <ul class="divide-y divide-gray-200">
    <li 
      v-for="(expense, index) in expenses" 
      :key="index" 
      class="py-3"
    >
      <ExpenseItem
        :expense="expense"
        @edit="$emit('edit', index)"
        @delete="$emit('delete', index)"
      />
    </li>
  </ul>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue'
import ExpenseItem from './ExpenseItem.vue'

defineProps({
  expenses: {
    type: Array,
    required: true
  }
})

defineEmits(['edit', 'delete'])
</script>
