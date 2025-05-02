<!-- resources/js/pages/ChatbotPage.vue -->
<script setup>
import { ref } from 'vue';
import Chatbot from '../components/Chatbot.vue';
// import { useHead } from '@vueuse/head'; // Optional, for setting page title

// useHead({
//   title: 'AI Chatbot | Your App Name'
// });
</script>

<template>
  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
          <h1 class="text-2xl font-bold text-gray-900 mb-6">AI Chatbot</h1>
          <p class="mb-4 text-gray-600">Ask any question to our GitHub AI-powered assistant</p>
          
          <div class="mt-8">
            <Chatbot />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>