<template>
  <div class="flex items-center justify-center min-h-[80vh]">
    <div class="w-full max-w-md bg-white rounded-lg shadow-md p-8">
      <h2 class="text-2xl font-bold text-center text-blue-600 mb-6">Login</h2>
      <form @submit.prevent="handleLogin" class="space-y-4">
        <InputField id="email" label="Email" type="email" v-model="formData.email" />
        <InputField id="password" label="Password" type="password" v-model="formData.password" show-toggle/>
        <button type="submit"
          class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
          Login
        </button>
        <p class="text-center text-sm text-gray-600 mt-4">
          Don't have an account?
          <router-link to="/register" class="text-blue-600 hover:text-blue-700">Register here</router-link>
        </p>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '@/stores/userStore'
import InputField from '../components/Shared/InputField.vue'

const router = useRouter()

const userStore = useUserStore()

const formData = reactive({
  email: '',
  password: '',
})

const handleLogin = async () => {
  try {
    await userStore.loginUser(formData)
    router.push('/')
  } catch (error) {
    console.error('Login failed:', error)
  }
}
</script>