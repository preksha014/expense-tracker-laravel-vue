import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import moment from 'moment'
import api from '@/services/api'

export const useExpenseStore = defineStore('expense', () => {
  const expenses = ref([])
  const isLoading = ref(false)
  const error = ref(null)

  const totalExpense = computed(() => {
    return expenses.value.reduce((sum, e) => sum + parseFloat(e.amount), 0)
  })

  const highestExpense = computed(() => {
    if (!expenses.value.length) return { name: 'None', amount: 0 }
    return expenses.value.reduce((max, e) =>
      parseFloat(e.amount) > parseFloat(max.amount) ? e : max, expenses.value[0])
  })

  const currentMonthExpense = computed(() => {
    const currentMonth = moment().format("YYYY-MM")
    return expenses.value
      .filter(expense => moment(expense.date).format("YYYY-MM") === currentMonth)
      .reduce((total, expense) => total + parseFloat(expense.amount), 0)
  })

  const recentExpenses = computed(() => {
    return [...expenses.value]
      .sort((a, b) => moment(b.date).valueOf() - moment(a.date).valueOf())
      .slice(0, 5)
  })

  async function fetchExpenses() {
    isLoading.value = true
    error.value = null
    try {
      const response = await api.get('/expenses')
      expenses.value = Array.isArray(response.data) ? response.data : []
      console.log('Fetched expenses:', expenses.value)
      return expenses.value
    } catch (err) {
      console.error('Error fetching expenses:', err)
      error.value = 'Failed to load expenses'
      return []
    } finally {
      isLoading.value = false
    }
  }

  async function addExpense(expense) {
    expense.amount = parseFloat(expense.amount)
    isLoading.value = true
    error.value = null
    try {
      const response = await api.post('/expenses', expense)
      console.log(response.data)
      if (response.data) {
        expenses.value.push(response.data.expense)
        return response.data.expense
      }
    } catch (err) {
      console.error('Failed to add expense:', err)
      error.value = 'Failed to add expense'
    } finally {
      isLoading.value = false
    }
  }

  async function updateExpense(expenseId, updatedData) {
    try {
      const response = await api.put(`/expenses/${expenseId}`, updatedData);
  
      console.log('Response:', response);
  
      const updatedExpense = response.data.expense;
  
      // Update local state
      const index = expenses.value.findIndex(e => e.id === expenseId);
      if (index !== -1) {
        expenses.value[index] = updatedExpense;
      }
  
      return updatedExpense;
    } catch (error) {
      console.error('Error updating expense:', error);
      throw error;
    }
  }
  

  async function deleteExpense(expenseId) {
    try {
      await api.delete(`/expenses/${expenseId}`); // Make sure this matches your Laravel route

      // Remove the item from the local state
      const index = expenses.value.findIndex(exp => exp.id === expenseId)
      if (index !== -1) {
        expenses.value.splice(index, 1)
      }
    } catch (error) {
      console.error('Failed to delete expense:', error);
      alert('Failed to delete the expense.');
    }
  }

  // function deleteExpense(index) {
  //   expenses.value.splice(index, 1)
  //   saveToLocalStorage()
  // }

  function updateExpenseGroup(oldGroupName, newGroupName) {
    expenses.value = expenses.value.map(expense => {
      if (expense.group === oldGroupName) {
        return { ...expense, group: newGroupName }
      }
      return expense
    })
    saveToLocalStorage()
  }

  function searchExpenses(query) {
    if (!query) return expenses.value
    return expenses.value.filter(e => e.name.toLowerCase().includes(query.toLowerCase()))
  }

  function saveToLocalStorage() {
    localStorage.setItem('expenses', JSON.stringify(expenses.value))
  }

  return {
    expenses,
    totalExpense,
    highestExpense,
    currentMonthExpense,
    recentExpenses,
    addExpense,
    updateExpense,
    deleteExpense,
    updateExpenseGroup,
    searchExpenses,
    fetchExpenses,
    isLoading
  }
})