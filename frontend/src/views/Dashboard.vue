<template>
  <div>
    <h1 class="text-2xl font-bold mb-6">Dashboard</h1>

    <!-- Summary Section with Cards -->
    <div class="bg-white p-6 mb-6">
      <h2 class="text-xl font-bold mb-4">Summary</h2>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Total Expense Card -->
        <div class="bg-blue-50 rounded-lg p-4 shadow-sm">
          <h3 class="text-lg font-semibold text-blue-700 mb-2">Total Expense</h3>
          <p class="text-2xl font-bold">₹ {{ expenseStore.totalExpense }}</p>
        </div>

        <!-- Monthly Expense Card -->
        <div class="bg-green-50 rounded-lg p-4 shadow-sm">
          <h3 class="text-lg font-semibold text-green-700 mb-2">Current Month</h3>
          <p class="text-2xl font-bold">₹ {{ expenseStore.currentMonthExpense }}</p>
        </div>

        <!-- Highest Expense Card -->
        <div class="bg-purple-50 rounded-lg p-4 shadow-sm">
          <h3 class="text-lg font-semibold text-purple-700 mb-2">Highest Expense</h3>
          <p class="text-2xl font-bold">₹ {{ expenseStore.highestExpense.amount }}</p>
          <p class="text-sm text-gray-600">{{ expenseStore.highestExpense.name }}</p>
        </div>
      </div>
    </div>

    <!-- Recent Expenses List -->
    <div class="bg-white p-6 mb-6">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Recent Expenses</h2>
        <router-link to="/expenses" class="text-blue-500 hover:text-blue-700 font-medium">
          View All →
        </router-link>
      </div>

      <ExpenseList :expenses="expenseStore.recentExpenses" />

      <div v-if="expenseStore.recentExpenses.length === 0" class="text-center py-4 text-gray-500">
        No recent expenses found.
      </div>
    </div>
  </div>
</template>

<script setup>
import { useExpenseStore } from '../stores/expenseStore'
import ExpenseList from '../components/Expenses/ExpenseList.vue'

const expenseStore = useExpenseStore()
</script>