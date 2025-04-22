import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  {
    path: '/',
    name: 'Dashboard',
    component: ()=>import ("@/views/Dashboard.vue")
  },
  {
    path: '/expenses',
    name: 'Expenses',
    component: ()=>import("@/views/Expenses.vue")
  },
  {
    path: '/groups',
    name: 'Groups',
    component: ()=>import("@/views/Groups.vue")
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router