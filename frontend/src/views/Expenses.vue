<template>
  <AuthLayout>
    <div>
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Expenses</h1>
        <button @click="openExpenseModal" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
          Add Expense
        </button>
      </div>

      <!-- Export Expenses -->
      <div class="flex gap-3 mb-4 justify-end">
        <button @click="exportToCSV()" :disabled="isCSVExporting"
          class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 flex items-center disabled:opacity-50 disabled:cursor-not-allowed">
          <span class="mr-1">{{ isCSVExporting ? 'Exporting...' : 'Export to CSV' }}</span>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 10v6m0 0l-3-3m3 3l3-3m-9 0H3.6a1.2 1.2 0 01-1.2-1.2V4.2A1.2 1.2 0 013.6 3h16.8a1.2 1.2 0 011.2 1.2v12.6a1.2 1.2 0 01-1.2 1.2H12" />
          </svg>
        </button>
        <button @click="exportToPDF()" :disabled="isPDFExporting"
          class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 flex items-center">
          <span class="mr-1">{{ isPDFExporting ? 'Exporting...' : 'Export to PDF' }}</span>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 10v6m0 0l-3-3m3 3l3-3m2-8H5a2 2 0 00-2 2v12a2 2 0 002 2h14a2 2 0 002-2V7a2 2 0 00-2-2z" />
          </svg>
        </button>
      </div>

      <!-- Filter Component -->
      <expense-filter :search-query="filters.searchQuery" :group-filter="filters.groupFilter"
        :month-filter="filters.monthFilter" :groups="groupStore.groups" @filter-change="updateFilters" />

      <!-- Expenses List -->
      <div class="bg-white p-4 shadow">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-bold">Expense List</h2>
          <p><span class="font-medium">Total:</span> ₹{{ totalAmount }}</p>
        </div>

        <expense-list :expenses="filteredExpenses" @edit="editExpense" @delete="showDeleteExpenseModal" />

        <p v-if="!filteredExpenses.length" class="text-center py-4 text-gray-500">
          No expenses found.
        </p>
      </div>

      <!-- Shared Modal Components -->
      <expense-modal v-if="showExpenseModal" :expense="editingExpense" :groups="groupStore.groups" @save="saveExpense"
        @close="closeExpenseModal" />

      <delete-modal v-if="showDeleteModal" title="Delete Expense"
        message="Are you sure you want to delete this expense? This action cannot be undone."
        @confirm="handleDeleteConfirm" @cancel="showDeleteModal = false" />
    </div>
  </AuthLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watchEffect } from 'vue'
import { useExpenseStore } from '../stores/expenseStore'
import { useGroupStore } from '../stores/groupStore'
import AuthLayout from '@/layouts/AuthLayout.vue'
import ExpenseList from '../components/Expenses/ExpenseList.vue'
import ExpenseFilter from '../components/Expenses/ExpenseFilter.vue'
import ExpenseModal from '../components/Shared/ExpenseModal.vue'
import DeleteModal from '../components/Shared/DeleteModal.vue'
import moment from 'moment'
import { useFileExport } from '@/composables/useFileExport';

const { exportFile, isCSVExporting, isPDFExporting } = useFileExport();

function exportToCSV() {
  exportFile('/expenses/export-csv', 'csv', 'expenses.csv', isCSVExporting);
}

function exportToPDF() {
  exportFile('/expenses/export-pdf', 'pdf', 'expenses.pdf', isPDFExporting);
}

const expenseStore = useExpenseStore()
const groupStore = useGroupStore()

// Group all filters in a reactive object
const filters = reactive({
  searchQuery: '',
  groupFilter: 0,
  monthFilter: ''
})

const filteredExpenses = ref([])
const showExpenseModal = ref(false)
const showDeleteModal = ref(false)
const editingExpense = ref(null)
const editingIndex = ref(-1)
const deletingIndex = ref(-1)

// Computed total amount
const totalAmount = computed(() =>
  filteredExpenses.value.reduce((sum, expense) => sum + Number(expense.amount), 0).toFixed(2)
)

// Use watchEffect to automatically apply filters when any filter changes
watchEffect(() => {
  applyFilters()
})

// Filter expenses based on current filters
function applyFilters() {
  filteredExpenses.value = expenseStore.expenses.filter(expense => {
    const matchesSearch = !filters.searchQuery ||
      expense.name.toLowerCase().includes(filters.searchQuery.toLowerCase())

    const matchesGroup = !filters.groupFilter || expense.group_id === filters.groupFilter;

    const matchesMonth = !filters.monthFilter ||
      moment(expense.date).format('YYYY-MM') === filters.monthFilter

    return matchesSearch && matchesGroup && matchesMonth
  })
}

// Update filters from the filter component
function updateFilters(newFilters) {
  Object.assign(filters, newFilters)
}

// Handle expense operations
function openExpenseModal() {
  editingExpense.value = {
    name: '',
    amount: '',
    date: moment().format('YYYY-MM-DD'),
    group: ''
  }
  editingIndex.value = -1
  showExpenseModal.value = true
}

function closeExpenseModal() {
  showExpenseModal.value = false
  editingExpense.value = null
}

function editExpense(index) {
  const expense = { ...filteredExpenses.value[index] }
  const realIndex = findExpenseIndex(expense)

  editingExpense.value = expense
  editingIndex.value = realIndex
  showExpenseModal.value = true
}

function saveExpense(expense) {
  if (editingExpense.value?.id) {
    expenseStore.updateExpense(editingExpense.value.id, expense)
  } else {
    expenseStore.addExpense(expense)
  }

  closeExpenseModal()
}

function showDeleteExpenseModal(index) {
  deletingIndex.value = index
  showDeleteModal.value = true
}

function handleDeleteConfirm() {
  const expense = filteredExpenses.value[deletingIndex.value]

  if (expense && expense.id) {
    expenseStore.deleteExpense(expense.id)
  }

  showDeleteModal.value = false
}

function findExpenseIndex(expense) {
  return expenseStore.expenses.findIndex(e =>
    e.name === expense.name &&
    e.amount === expense.amount &&
    e.date === expense.date &&
    e.group === expense.group
  )
}

onMounted(async () => {
  await expenseStore.fetchExpenses();
  await groupStore.fetchGroups();
  applyFilters();
})
</script>