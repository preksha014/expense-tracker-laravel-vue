import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/api'
import { useRouter } from 'vue-router'

export const useUserStore = defineStore('user', () => {
    // State
    const users = ref([])
    const token = localStorage.getItem('token') || null
    const router = useRouter()
    const isLoggedIn = ref(!!token)

    // Actions
    async function registerUser(credentials) {
        try {
            isLoggedIn.value = true
            const response = await api.post('/register', { ...credentials })
            if (response.data.data.token) {
                console.log(response.data)
                localStorage.setItem('token', response.data.data.token)
                users.value = response.data.data.user
                return response.data.data.user
            }
        }
        catch (error) {
            console.error('Error during registration:', error)
            throw error
        }
    }

    async function loginUser(credentials) {
        try {
            isLoggedIn.value = true
            const response = await api.post('/login', { ...credentials })
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

