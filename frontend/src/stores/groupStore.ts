import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'
import { useExpenseStore } from './expenseStore'

export const useGroupStore = defineStore('group', () => {
  // State
  const groups = ref([])
  const isLoading = ref(false)
  const error = ref(null)

  // Getters
  const groupList = computed(() => groups.value)

  const groupExpenses = computed(() => (groupName) => {
    const expenseStore = useExpenseStore()
    if (!groupName) return 0

    return expenseStore.expenses
      .filter(e => e.group === groupName)
      .reduce((sum, e) => sum + e.amount, 0)
  })

  // Actions
  async function fetchGroups() {
    isLoading.value = true
    error.value = null
    try {
      const response = await api.get('/groups')
      groups.value = Array.isArray(response.data.data) ? response.data.data : []
      return groups.value
    } catch (err) {
      console.error('Error fetching groups:', err)
      error.value = 'Failed to load groups'
      return []
    } finally {
      isLoading.value = false
    }
  }

  async function addGroup(name) {
    error.value = null
    isLoading.value = true

    try {

      const response = await api.post('/groups', { name })
      const group = response.data.data

      if (response.data.data) {
        groups.value.push(group)
        return group
      }
    } catch (err) {
      console.error('Failed to add group:', err)
      error.value = err.response?.data?.message || 'Failed to add group'
      throw error.value
    }
  }

  async function updateGroup(groupId, newName) {
    try {
      const response = await api.put(`/groups/${groupId}`, { name: newName })

      const updatedGroup = response.data.data

      const index = groups.value.findIndex(g => g.id === groupId)
      if (index !== -1) {
        groups.value[index] = updatedGroup
      }

      return updatedGroup
    } catch (error) {
      console.error('Error updating group:', error)
      throw error
    }
  }

  async function deleteGroup(groupId) {
    try {
      await api.delete(`/groups/${groupId}`)

      const index = groups.value.findIndex(g => g.id === groupId)
      if (index !== -1) {
        groups.value.splice(index, 1)
      }

      const expenseStore = useExpenseStore()
      await expenseStore.fetchExpenses()

    } catch (error) {
      console.error('Failed to delete group:', error)
      alert('Failed to delete the group.')
    }
  }

  function groupExists(name) {
    return groups.value.some(g => g.name.toLowerCase() === name.toLowerCase())
  }

  return {
    groups,
    groupList,
    groupExpenses,
    isLoading,
    error,
    addGroup,
    updateGroup,
    deleteGroup,
    groupExists,
    fetchGroups,
  }
})