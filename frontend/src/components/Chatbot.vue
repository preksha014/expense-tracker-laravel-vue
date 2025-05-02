<!-- src/components/Chatbot.vue -->
<script setup>
import { ref, onMounted, watch } from 'vue';
import api from '@/services/api'

const messages = ref([]);
const newMessage = ref('');
const isLoading = ref(false);
const systemPrompt = ref('You are a helpful assistant.');
const showSettings = ref(false);

function addMessage(sender, content, isError = false) {
  messages.value.push({
    id: Date.now(),
    sender,
    content,
    isError,
    timestamp: new Date().toLocaleTimeString()
  });
}

async function sendMessage() {
  if (!newMessage.value.trim()) return;
  
  // Add user message to chat
  addMessage('user', newMessage.value);
  
  // Store the message and clear input
  const messageText = newMessage.value;
  newMessage.value = '';
  
  // Show loading state
  isLoading.value = true;
  
  try {
    // Send request to backend with system prompt
    const response = await api.post('/chat', {
      message: messageText,
      systemPrompt: systemPrompt.value
    });
    
    // Process response from GitHub AI
    if (response.data.error) {
      addMessage('bot', 'Error: ' + response.data.message, true);
    } else {
      addMessage('bot', response.data.response);
    }
  } catch (error) {
    console.error('Error calling chatbot API:', error);
    addMessage('bot', 'Sorry, there was an error connecting to the AI service.', true);
  } finally {
    isLoading.value = false;
  }
}

function toggleSettings() {
  showSettings.value = !showSettings.value;
}

// Scroll to bottom of chat when new messages arrive
function scrollToBottom() {
  setTimeout(() => {
    const chatContainer = document.querySelector('.chat-messages');
    if (chatContainer) {
      chatContainer.scrollTop = chatContainer.scrollHeight;
    }
  }, 50);
}

// Watch for changes to messages and scroll down
watch(messages, () => {
  scrollToBottom();
}, { deep: true });

onMounted(() => {
  // Welcome message
  addMessage('bot', 'Hello! How can I help you today?');
});
</script>

<template>
  <div class="flex flex-col bg-white rounded-lg shadow-lg h-96 max-w-lg mx-auto">
    <!-- Chat header -->
    <div class="bg-gray-800 text-white px-4 py-3 rounded-t-lg flex justify-between items-center">
      <div class="flex items-center">
        <div class="h-3 w-3 bg-green-500 rounded-full mr-2"></div>
        <h3 class="font-medium">GitHub AI Chatbot</h3>
      </div>
      <button 
        @click="toggleSettings" 
        class="text-gray-300 hover:text-white focus:outline-none"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
        </svg>
      </button>
    </div>
    
    <!-- Settings panel (conditionally rendered) -->
    <div v-if="showSettings" class="bg-gray-100 p-4 border-b">
      <h4 class="text-sm font-semibold mb-2">Bot Settings</h4>
      <div class="mb-2">
        <label class="block text-sm text-gray-700 mb-1">System Prompt</label>
        <textarea
          v-model="systemPrompt"
          class="w-full p-2 border rounded text-sm"
          rows="2"
        ></textarea>
      </div>
      <p class="text-xs text-gray-500">This defines how the AI will behave.</p>
    </div>
    
    <!-- Chat messages -->
    <div class="flex-1 p-4 overflow-y-auto chat-messages">
      <div 
        v-for="message in messages" 
        :key="message.id"
        class="mb-3"
      >
        <div 
          class="p-3 rounded-lg max-w-xs"
          :class="{
            'ml-auto bg-blue-500 text-white': message.sender === 'user',
            'bg-gray-200': message.sender === 'bot' && !message.isError,
            'bg-red-100 text-red-800': message.sender === 'bot' && message.isError
          }"
        >
          {{ message.content }}
          <div 
            class="text-xs opacity-70 mt-1"
            :class="{ 'text-blue-200': message.sender === 'user' }"
          >
            {{ message.timestamp }}
          </div>
        </div>
      </div>
      
      <!-- Loading indicator -->
      <div v-if="isLoading" class="flex items-center space-x-2 mb-3">
        <div class="bg-gray-200 p-3 rounded-lg">
          <div class="flex space-x-1">
            <div class="h-2 w-2 bg-gray-500 rounded-full animate-bounce"></div>
            <div class="h-2 w-2 bg-gray-500 rounded-full animate-bounce delay-75"></div>
            <div class="h-2 w-2 bg-gray-500 rounded-full animate-bounce delay-150"></div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Chat input -->
    <div class="border-t p-3">
      <form @submit.prevent="sendMessage" class="flex">
        <input
          v-model="newMessage"
          type="text"
          placeholder="Type your message here..."
          class="flex-1 border rounded-l-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
        <button
          type="submit"
          class="bg-blue-500 text-white px-4 py-2 rounded-r-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          Send
        </button>
      </form>
    </div>
  </div>
</template>