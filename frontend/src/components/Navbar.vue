<template>
    <nav class="bg-blue-600 text-white shadow-md">
      <div class="container mx-auto px-6">
        <div class="flex items-center justify-between h-16">
          <div class="flex items-center">
            <router-link to="/" class="flex-shrink-0">
              <span class="text-xl font-bold tracking-wide text-white">Expense Tracker</span>
            </router-link>
          </div>
          <div class="flex items-center space-x-2">
            <router-link 
              v-for="item in navigationItems" 
              :key="item.name" 
              :to="item.path" 
              class="px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 ease-in-out"
              :class="isActive(item.path) ? 'bg-blue-700 text-white' : 'text-white hover:bg-blue-500'">
              {{ item.name }}
            </router-link>
          </div>
        </div>
      </div>
    </nav>
  </template>
  
  <script setup lang="ts">
  import { ref, computed } from 'vue'
  import { useRoute } from 'vue-router'
  import { useUserStore } from '@/stores/userStore'

  const userStore = useUserStore()
  const route = useRoute()
  
  const navigationItems = computed(() => {
    if (userStore.isLoggedIn) {
      return [
        { name: 'Dashboard', path: '/' },
        { name: 'Expenses', path: '/expenses' },
        { name: 'Groups', path: '/groups' },
        { name: 'Logout', path: '/logout', action: 'logout'}
      ]
    } else {
      return [
        { name: 'Login', path: '/login' },
        { name: 'Register', path: '/register' }
      ]
    }
  })
  
  function isActive(path) {
    if (path === '/' && route.path === '/') {
      return true
    }
    return route.path.startsWith(path) && path !== '/'
  }
  </script>