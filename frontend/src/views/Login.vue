<template>
  <div class="flex items-center justify-center min-h-[80vh]" data-v-inspector="src/views/Login.vue:2:3">
    <div class="w-full max-w-md bg-white rounded-lg shadow-md p-8" data-v-inspector="src/views/Login.vue:3:5">
      <h2 class="text-2xl font-bold text-center text-blue-600 mb-6" data-v-inspector="src/views/Login.vue:4:7">Login</h2>
      <form @submit.prevent="handleLogin" class="space-y-4" data-v-inspector="src/views/Login.vue:5:7">
        <InputField
          id="email"
          label="Email"
          type="email"
          v-model="formData.email"
          :error="v$.email.$error ? v$.email.$errors[0].$message : ''"
          data-v-inspector="src/views/Login.vue:6:9"
        />
        <InputField
          id="password"
          label="Password"
          type="password"
          v-model="formData.password"
          :error="v$.password.$error ? v$.password.$errors[0].$message : ''"
          show-toggle
          data-v-inspector="src/views/Login.vue:8:9"
        />
        <button
          type="submit"
          class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
          data-v-inspector="src/views/Login.vue:10:9"
        >
          Login
        </button>
        <!-- Display backend error message -->
        <p v-if="errorMessage" class="text-center text-sm text-red-600 mt-4" data-v-inspector="src/views/Login.vue:14:9">
          {{ errorMessage }}
        </p>
        <p class="text-center text-sm text-gray-600 mt-4" data-v-inspector="src/views/Login.vue:16:9">
          Don't have an account?
          <router-link to="/register" class="text-blue-600 hover:text-blue-700" data-v-inspector="src/views/Login.vue:18:11">
            Register here
          </router-link>
        </p>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import useVuelidate from '@vuelidate/core';
import { required } from '@vuelidate/validators';
import { useUserStore } from '@/stores/userStore';
import InputField from '../components/Shared/InputField.vue';

const router = useRouter();
const userStore = useUserStore();

// Reactive form data
const formData = reactive({
  email: '',
  password: '',
});

const errorMessage = ref(null);

// Validation rules
const rules = computed(() => {
  return {
    email: { required },
    password: { required },
  };
});

const v$ = useVuelidate(rules, formData);

const handleLogin = async () => {
  const isValid = await v$.value.$validate();

  if (!isValid) {
    console.log('Validation failed:', v$.value.$errors);
    return;
  }
  try {
    await userStore.loginUser(formData);
    errorMessage.value = null;
    router.push('/');
  } catch (error) {
    errorMessage.value =
      error.response?.data?.error || 'Login failed. Please check your credentials and try again.';
  }
};
</script>