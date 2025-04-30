import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import moment from 'moment'
import api from '@/services/api'

export const useExpenseStore = defineStore('expense', () => {
  const expenses = ref<Expense[]>([])
  const isLoading = ref(false)
  const error = ref<string | null>(null)

  const totalExpense = computed(() => {
    return expenses.value.reduce((sum, e) => sum + parseFloat(e.amount.toString()), 0)
  })

  const highestExpense = computed(() => {
    if (!expenses.value.length) return { name: 'None', amount: 0 }
    return expenses.value.reduce((max, e) =>
      parseFloat(e.amount.toString()) > parseFloat(max.amount.toString()) ? e : max, expenses.value[0])
  })

  const currentMonthExpense = computed(() => {
    const currentMonth = moment().format("YYYY-MM")
    return expenses.value
      .filter(expense => moment(expense.date).format("YYYY-MM") === currentMonth)
      .reduce((total, expense) => total + parseFloat(expense.amount.toString()), 0)
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
      expenses.value = Array.isArray(response.data.data) ? response.data.data : []
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

  interface Expense {
    id?: number;
    name: string;
    amount: number;
    date: string;
    group?: string;
    [key: string]: any;
  }

  async function addExpense(expense: Expense): Promise<Expense | undefined> {
    expense.amount = parseFloat(expense.amount.toString());
    isLoading.value = true;
    error.value = null;
    try {
      const response = await api.post<{ data: Expense }>('/expenses', expense);
      if (response.data.data) {
        expenses.value.push(response.data.data);
        return response.data.data;
      }
    } catch (err) {
      console.error('Failed to add expense:', err);
      error.value = 'Failed to add expense';
    } finally {
      isLoading.value = false;
    }
  }

  interface UpdatedExpenseData {
    name?: string;
    amount?: number;
    date?: string;
    group?: string;
    [key: string]: any;
  }

  async function updateExpense(expenseId: number, updatedData: UpdatedExpenseData): Promise<Expense | undefined> {
    try {
      const response = await api.put<{ data: Expense }>(`/expenses/${expenseId}`, updatedData);

      const updatedExpense: Expense = response.data.data;

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


  async function deleteExpense(expenseId: number): Promise<void> {
    try {
      await api.delete(`/expenses/${expenseId}`);

      const index = expenses.value.findIndex((exp: { id?: number }) => exp.id === expenseId);
      if (index !== -1) {
        expenses.value.splice(index, 1);
      }
    } catch (error) {
      console.error('Failed to delete expense:', error);
      alert('Failed to delete the expense.');
    }
  }

  function updateExpenseGroup(oldGroupName: string, newGroupName: string): void {
    expenses.value = expenses.value.map((expense: Expense) => {
      if (expense.group === oldGroupName) {
        return { ...expense, group: newGroupName }
      }
      return expense
    })
  }

  function searchExpenses(query: string): Expense[] {
    if (!query) return expenses.value
    return expenses.value.filter((e: Expense) => e.name.toLowerCase().includes(query.toLowerCase()))
  }

  return {
    expenses,
    totalExpense,
    highestExpense,
    currentMonthExpense,
    recentExpenses,
    isLoading,
    error,
    addExpense,
    updateExpense,
    deleteExpense,
    updateExpenseGroup,
    searchExpenses,
    fetchExpenses
  }
})