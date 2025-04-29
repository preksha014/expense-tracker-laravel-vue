import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  {
    path: '/',
    name: 'Dashboard',
    component: () => import("@/views/Dashboard.vue"),
  },
  {
    path: '/expenses',
    name: 'Expenses',
    component: () => import("@/views/Expenses.vue"),
  },
  {
    path: '/groups',
    name: 'Groups',
    component: () => import("@/views/Groups.vue"),
  },
  {
    path: '/register',
    name: 'Register',
    component: () => import("@/views/Register.vue")
  },
  {
    path: '/login',
    name: 'Login',
    component: () => import("@/views/Login.vue")
  },
  {
    path: '/logout',
    name: 'Logout',
    beforeEnter: async (to, from, next) => {
      const userStore = (await import('@/stores/userStore')).useUserStore()
      await userStore.logoutUser()
      next('/login')
    }
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: () => import("@/views/NotFound.vue")
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router