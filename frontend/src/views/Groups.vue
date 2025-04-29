<template>
  <AuthLayout>
    <div>
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Groups</h1>
        <button @click="openGroupModal" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
          Add Group
        </button>
      </div>

      <!-- Groups List -->
      <div class="bg-white p-4 shadow">
        <h2 class="text-xl font-bold mb-4">Group List</h2>
        <group-list :groups="groupStore.groups" @edit="editGroup" @delete="showDeleteGroupModal" />

        <p v-if="groupStore.groups.length === 0" class="text-center py-4 text-gray-500">
          No groups found. Create a group to get started.
        </p>
      </div>

      <!-- Shared Modal Components -->
      <group-modal v-if="showGroupModal" :group="editingGroup" @close="closeGroupModal" />

      <delete-modal v-if="showDeleteModal" title="Delete Group"
        message="Are you sure you want to delete this group? All expenses in this group will also be deleted."
        @confirm="handleDeleteConfirm" @cancel="showDeleteModal = false" />
    </div>
  </AuthLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useGroupStore } from '../stores/groupStore'
import { storeToRefs } from 'pinia'
import AuthLayout from '@/layouts/AuthLayout.vue'
import GroupList from '../components/Groups/GroupList.vue'
import GroupModal from '../components/Shared/GroupModal.vue'
import DeleteModal from '../components/Shared/DeleteModal.vue'

const groupStore = useGroupStore()
const { groups } = storeToRefs(groupStore)

const showGroupModal = ref(false)
const showDeleteModal = ref(false)
const editingGroup = ref(null)
const editingIndex = ref(-1)
const deletingIndex = ref(-1)

function openGroupModal() {
  editingGroup.value = { name: '' }
  editingIndex.value = -1
  showGroupModal.value = true
}

function closeGroupModal() {
  showGroupModal.value = false
  editingGroup.value = null
}

function editGroup(index) {
  console.log('Group data:', groups.value[index])
  editingGroup.value = groups.value[index]
  editingIndex.value = index
  showGroupModal.value = true
}

function showDeleteGroupModal(index) {
  deletingIndex.value = index
  showDeleteModal.value = true
}

function handleDeleteConfirm() {
  const group = groups.value[deletingIndex.value]

  if (group && group.id) {
    groupStore.deleteGroup(group.id)
  }

  showDeleteModal.value = false
}

onMounted(async () => {
  await groupStore.fetchGroups()
})
</script>