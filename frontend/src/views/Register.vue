<template>
  <div class="flex items-center justify-center min-h-[80vh]">
    <div class="w-full max-w-md bg-white rounded-lg shadow-md p-8">
      <h2 class="text-2xl font-bold text-center text-blue-600 mb-6">Register</h2>
      <form @submit.prevent="handleRegister" class="space-y-4">
        <InputField id="name" label="Name" type="text" v-model="formData.name"
          :error="v$.name.$error ? v$.name.$errors[0].$message : ''" />
        <InputField id="email" label="Email" type="email" v-model="formData.email"
          :error="v$.email.$error ? v$.email.$errors[0].$message : ''" />
        <InputField id="password" label="Password" type="password" v-model="formData.password"
          :error="v$.password.$error ? v$.password.$errors[0].$message : ''" show-toggle />
        <InputField id="password_confirmation" label="Confirm Password" type="password"
          v-model="formData.password_confirmation"
          :error="v$.password_confirmation.$error ? v$.password_confirmation.$errors[0].$message : ''" show-toggle />
        <button @click.prevent="handleRegister" type="submit"
          class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
          Register
        </button>
        <p class="text-center text-sm text-gray-600 mt-4">
          Already have an account?
          <router-link to="/login" class="text-blue-600 hover:text-blue-700">Login here</router-link>
        </p>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue'
import { useRouter } from 'vue-router'
import useVuelidate from '@vuelidate/core'
import { required } from '@vuelidate/validators'
import { useUserStore } from '@/stores/userStore'
import InputField from '../components/Shared/InputField.vue'

const router = useRouter()

const userStore = useUserStore()

const formData = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

const rules = {
  name: { required },
  email: { required },
  password: { required },
  password_confirmation: { required }
}

const v$ = useVuelidate(rules, formData)

const handleRegister = async () => {
  const isValid = await v$.value.$validate()

  if (!isValid) {
    console.log('Validation failed:', v$.value.$errors)
    return
  }

  try {
    await userStore.registerUser(formData);
    router.push('/')
  } catch (error) {
    console.error('Registration failed:', error)
  }
}
</script>