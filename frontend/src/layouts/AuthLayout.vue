<template>
    <div>
        <main class="bg-gray-100">
            <slot />
        </main>
    </div>
</template>

<script setup>
import { useUserStore } from '@/stores/userStore';
import { useRouter } from 'vue-router';

const router = useRouter();
const userStore = useUserStore();

if (!userStore.isLoggedIn) {
    router.push('/login');
}
</script>