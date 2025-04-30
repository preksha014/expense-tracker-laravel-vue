import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/api'
import { useRouter } from 'vue-router'

export const useUserStore = defineStore('user', () => {
    // State
    const users = ref<User | null>(null)
    const token = localStorage.getItem('token') || null
    const router = useRouter()
    const isLoggedIn = ref(!!token)

    // Actions
    interface User {
        id: number;
        name: string;
        email: string;
    }

    interface RegisterResponse {
        data: {
            data: {
                token: string;
                user: User;
            };
        };
    }

    interface RegisterCredentials {
        name: string;
        email: string;
        password: string;
    }

    async function registerUser(credentials: RegisterCredentials): Promise<User | undefined> {
        try {
            isLoggedIn.value = true;
            const response: RegisterResponse = await api.post('/register', { ...credentials });
            if (response.data.data.token) {
                console.log(response.data);
                localStorage.setItem('token', response.data.data.token);
                users.value = response.data.data.user;
                return response.data.data.user;
            }
        } catch (error) {
            console.error('Error during registration:', error);
            throw error;
        }
    }

    interface LoginResponse {
        data: {
            data: {
                token: string;
                user: User;
            };
        };
    }

    interface LoginCredentials {
        email: string;
        password: string;
    }

    async function loginUser(credentials: LoginCredentials): Promise<User | undefined> {
        try {
            isLoggedIn.value = true
            const response: LoginResponse = await api.post('/login', { ...credentials })
            if (response.data.data.token) {
                localStorage.setItem('token', response.data.data.token)
                users.value = response.data.data.user
                return response.data.data.user
            }
        }
        catch (error) {
            console.error('Error during login:', error)
            throw error
        }
    }
    async function logoutUser() {
        try {
            const response = await api.post('/logout')
            if (response.data.type === 'success') {
                localStorage.removeItem('token')
                isLoggedIn.value = false
                users.value = null
                await router.push('/login')
            }
        } catch (error) {
            console.error('Error during logout:', error)
            throw error
        }
    }
    return {
        users,
        token,
        isLoggedIn,
        registerUser,
        loginUser,
        logoutUser,
    }
})

