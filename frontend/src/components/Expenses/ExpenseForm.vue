<template>
  <form @submit.prevent="handleSubmit">
    <InputField id="name" v-model="formData.name" label="Name" placeholder="Enter expense name"
      :error="v$.name.$error ? v$.name.$errors[0].$message : ''" />

    <InputField id="amount" v-model="formData.amount" label="Amount (₹)" type="number" step="0.01" min="0.01"
      placeholder="Enter amount" :error="v$.amount.$error ? v$.amount.$errors[0].$message : ''" />

    <InputField id="date" v-model="formData.date" label="Date" type="date"
      :error="v$.date.$error ? v$.date.$errors[0].$message : ''" />

    <div class="mb-6">
      <label for="group" class="block text-sm font-medium text-gray-700 mb-1">Group</label>
      <select id="group" v-model="formData.group_id" class="w-full p-2 border border-gray-300 rounded">
        <option value="0">Select a group</option>
        <option v-for="group in groups" :key="group.id" :value="group.id">
          {{ group.name }}
        </option>
      </select>
      <span v-if="v$.group_id.$error" class="text-red-500 text-sm mt-1">
        {{ v$.group_id.$errors[0].$message }}
      </span>
    </div>

    <div class="flex justify-end space-x-3">
      <button type="button" @click="$emit('close')" class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-100">
        Cancel
      </button>
      <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
        {{ isEditing ? 'Update' : 'Add' }}
      </button>
    </div>
  </form>
</template>

<script setup>
import { defineProps, defineEmits, ref, computed, onMounted } from 'vue'
import useVuelidate from '@vuelidate/core'
import { required, decimal, minValue } from '@vuelidate/validators'
import moment from 'moment'
import InputField from '../Shared/InputField.vue'

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

const formData = ref({
  name: '',
  amount: '',
  date: moment().format('YYYY-MM-DD'),
  group_id: 0
})

// Validation rules
const rules = computed(() => {
  return {
    name: { required },
    amount: { required, decimal, minValue: minValue(0.01) },
    date: { required },
    group_id: { required }
  }
})

const v$ = useVuelidate(rules, formData)

// Computed to check if editing
const isEditing = computed(() => {
  return props.expense.id !== undefined ||
    (props.expense.name && props.expense.amount && props.expense.date)
})

// Format and set data on mount
onMounted(() => {
  if (isEditing.value) {
    formData.value = {
      ...props.expense,
      date: props.expense.date
        ? moment(props.expense.date).format('YYYY-MM-DD')
        : moment().format('YYYY-MM-DD')
    }
  } else {
    formData.value = {
      ...formData.value,
      date: moment().format('YYYY-MM-DD'),
      group_id: props.groups.length > 0 ? props.groups[0].id : 0
    }
  }
})

// Submit handler
async function handleSubmit() {
  const isValid = await v$.value.$validate()

  if (!isValid) {
    console.error('Form validation failed:', v$.value.$errors)
    return
  }

  const expenseData = {
    id: props.expense.id,
    name: formData.value.name,
    amount: Number(formData.value.amount),
    date: formData.value.date,
    group_id: formData.value.group_id
  }

  emit('save', expenseData)
}
</script>
