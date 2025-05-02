<template>
    <div>
        <main class="bg-gray-100">
            <slot />
            <button @click="handleChatbot"
                class="fixed bottom-6 right-6 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-full shadow-lg transition duration-300"
                title="Chat with assistant">
                💬
            </button>
        </main>
    </div>
</template>

<script setup>
import { useUserStore } from '@/stores/userStore';
import { useRouter } from 'vue-router';
import ChatbotPage from '@/views/ChatbotPage.vue';
const router = useRouter();
const userStore = useUserStore();

if (!userStore.isLoggedIn) {
    router.push('/login');
}

const handleChatbot = () => {
    router.push('/chatbot');
};
</script>