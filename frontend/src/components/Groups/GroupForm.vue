<template>
  <form @submit.prevent="handleSubmit">
    <InputField 
      id="name" 
      v-model="formData.name" 
      label="Group Name"
      :error="v$.name.$error ? v$.name.$errors[0].$message : ''" 
    />

    <div class="flex justify-end space-x-3 mt-4">
      <button 
        type="button" 
        @click="$emit('close')" 
        class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-100"
      >
        Cancel
      </button>
      <button 
        type="submit" 
        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
        :disabled="isLoading"
      >
        {{ isEditing ? 'Update' : 'Add' }}
      </button>
    </div>
  </form>
</template>

<script setup>
import { defineProps, defineEmits, ref, computed, onMounted } from 'vue'
import useVuelidate from '@vuelidate/core'
import { required, minLength, maxLength, helpers } from '@vuelidate/validators'
import { useGroupStore } from '../../stores/groupStore'
import InputField from '../Shared/InputField.vue'

const props = defineProps({
  group: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['save', 'close'])
const groupStore = useGroupStore()
const isLoading = ref(false)

const formData = ref({
  name: props.group?.name || ''
})

const rules = computed(() => ({
  name: { 
    required: helpers.withMessage('Group name is required', required),
    minLength: helpers.withMessage('Group name must be at least 2 characters', minLength(2)),
    maxLength: helpers.withMessage('Group name cannot exceed 50 characters', maxLength(50)),
    validFormat: helpers.withMessage('Group name can only contain letters, numbers, spaces and hyphens', helpers.regex(/^[\w\s-]+$/))
  }
}))

const v$ = useVuelidate(rules, formData)

const isEditing = computed(() => {
  return props.group.id !== undefined
})

// Form data is now initialized with props.group.name

async function handleSubmit() {
  const result = await v$.value.$validate()
  if (!result) return

  try {
    isLoading.value = true
    let response

    if (isEditing.value) {
      // console.log('Editing group:', index)
      response = await groupStore.updateGroup(props.group.id, formData.value.name)
    } else {
      const trimmedName = formData.value.name.trim()
      if (!trimmedName) {
        v$.value.name.$errors.push({ $message: 'Group name cannot be empty or just whitespace' })
        return
      }
      
      if (groupStore.groupExists(trimmedName)) {
        v$.value.name.$errors.push({ $message: 'Group name already exists!' })
        return
      }
      
      // Update the name to use the trimmed version
      formData.value.name = trimmedName
      response = await groupStore.addGroup(formData.value.name)
    }

    emit('save', response)
    emit('close')
  } catch (error) {
    console.error('Error saving group:', error)
    v$.value.name.$errors.push({ $message: error })
  } finally {
    isLoading.value = false
  }
}
</script>