<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded shadow-lg max-w-md w-full">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">{{ isEditing ? 'Edit Expense' : 'Add Expense' }}</h2>
        <button @click="$emit('close')" class="text-gray-500 hover:text-gray-700">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      
      <ExpenseForm 
        :expense="expense" 
        :groups="groups" 
        @save="handleSave" 
        @close="$emit('close')" 
      />
    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits, computed } from 'vue'
import ExpenseForm from '../Expenses/ExpenseForm.vue'

const props = defineProps({
  expense: {
    type: Object,
    required: true
  },
  groups: {
    type: Array,
    required: true
  }
})

const emit = defineEmits(['save', 'close'])

// Check if we're editing an existing expense
const isEditing = computed(() => {
  return props.expense.id !== undefined || 
         (props.expense.name && props.expense.amount && props.expense.date);
})

function handleSave(expenseData) {
  emit('save', expenseData)
}
</script>