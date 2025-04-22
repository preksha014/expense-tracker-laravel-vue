<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Expenses</h1>
      <button @click="openExpenseModal" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
        Add Expense
      </button>
    </div>

    <!-- Filter Component -->
    <expense-filter 
      :search-query="filters.searchQuery" 
      :group-filter="filters.groupFilter"
      :month-filter="filters.monthFilter" 
      :groups="groupStore.groups" 
      @filter-change="updateFilters" 
    />

    <!-- Expenses List -->
    <div class="bg-white p-4 shadow">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Expense List</h2>
        <p><span class="font-medium">Total:</span> ₹{{ totalAmount }}</p>
      </div>

      <expense-list 
        :expenses="filteredExpenses" 
        @edit="editExpense" 
        @delete="showDeleteExpenseModal" 
      />

      <p v-if="!filteredExpenses.length" class="text-center py-4 text-gray-500">
        No expenses found.
      </p>
    </div>

    <!-- Shared Modal Components -->
    <expense-modal 
      v-if="showExpenseModal" 
      :expense="editingExpense" 
      :groups="groupStore.groups" 
      @save="saveExpense" 
      @close="closeExpenseModal" 
    />

    <delete-modal
      v-if="showDeleteModal"
      title="Delete Expense"
      message="Are you sure you want to delete this expense? This action cannot be undone."
      @confirm="handleDeleteConfirm"
      @cancel="showDeleteModal = false"
    />
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watchEffect } from 'vue'
import { useExpenseStore } from '../stores/expenseStore'
import { useGroupStore } from '../stores/groupStore'
import ExpenseList from '../components/Expenses/ExpenseList.vue'
import ExpenseFilter from '../components/Expenses/ExpenseFilter.vue'
import ExpenseModal from '../components/Shared/ExpenseModal.vue'
import DeleteModal from '../components/Shared/DeleteModal.vue'
import moment from 'moment'

const expenseStore = useExpenseStore()
const groupStore = useGroupStore()

// Group all filters in a reactive object
const filters = reactive({
  searchQuery: '',
  groupFilter: '',
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

    const matchesGroup = !filters.groupFilter || expense.group === filters.groupFilter

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