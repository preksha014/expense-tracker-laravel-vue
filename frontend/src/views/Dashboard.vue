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

    <!-- Charts Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
      <ExpenseChart chart-id="monthlyTrend" title="Monthly Expense Trend" type="line" :data="monthlyTrendData" />
      <ExpenseChart chart-id="categoryDistribution" title="Expense by Category" type="doughnut"
        :data="categoryDistributionData" />
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

<script setup lang="ts">
import { onMounted, computed } from 'vue'
import { useExpenseStore } from '../stores/expenseStore'
import { useGroupStore } from '../stores/groupStore'
import ExpenseList from '../components/Expenses/ExpenseList.vue'
import ExpenseChart from '../components/Expenses/ExpenseChart.vue'
import moment from 'moment'

const expenseStore = useExpenseStore()
const groupStore = useGroupStore()

// Data for monthly trend chart
const monthlyTrendData = computed(() => {
  const last6Months = Array.from({ length: 6 }, (_, i) => {
    return moment().subtract(i, 'months').format('YYYY-MM')
  }).reverse()

  const monthlyTotals = last6Months.map(month => {
    const total = expenseStore.expenses
      .filter(expense => moment(expense.date).format('YYYY-MM') === month)
      .reduce((sum, expense) => sum + Number(expense.amount), 0)
    return total
  })

  return {
    labels: last6Months.map(month => moment(month).format('MMM YYYY')),
    datasets: [{
      label: 'Monthly Expenses',
      data: monthlyTotals,
      borderColor: '#3B82F6',
      backgroundColor: 'rgba(59, 130, 246, 0.1)',
      fill: true
    }]
  }
})

// Data for category distribution chart
const categoryDistributionData = computed(() => {
  const groupTotals = {}
  expenseStore.expenses.forEach(expense => {
    const group = groupStore.groups.find(g => g.id === expense.group_id)
    const groupName = group ? group.name : 'Uncategorized'
    groupTotals[groupName] = (groupTotals[groupName] || 0) + Number(expense.amount)
  })

  return {
    labels: Object.keys(groupTotals),
    datasets: [{
      data: Object.values(groupTotals),
      backgroundColor: [
        '#3B82F6', '#10B981', '#8B5CF6', '#F59E0B', '#EF4444',
        '#EC4899', '#6366F1', '#14B8A6', '#F97316', '#6B7280'
      ]
    }]
  }
})

onMounted(async () => {
  expenseStore.fetchExpenses(),
    groupStore.fetchGroups()
})
</script>