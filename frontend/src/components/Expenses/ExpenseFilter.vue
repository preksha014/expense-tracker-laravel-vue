<template>
    <div class="bg-white p-4 shadow mb-6">
      <h2 class="font-bold mb-3">Filters</h2>
      <div class="grid grid-cols-3 gap-4">
        <div>
          <label for="search" class="block mb-2">Search:</label>
          <input 
            id="search"
            type="text" 
            v-model="filters.searchQuery" 
            class="border p-2 w-full" 
            placeholder="Search expenses..."
            @input="emitFilters"
          >
        </div>
        <div>
          <label for="group" class="block mb-2">Group:</label>
          <select 
            id="group"
            v-model="filters.groupFilter" 
            class="border p-2 w-full"
            @change="emitFilters"
          >
            <option value="">All Groups</option>
            <option 
              v-for="group in groups" 
              :key="group.name" 
              :value="group.name"
            >
              {{ group.name }}
            </option>
          </select>
        </div>
        <div>
          <label for="month" class="block mb-2">Month:</label>
          <input 
            id="month"
            type="month" 
            v-model="filters.monthFilter" 
            class="border p-2 w-full"
            @change="emitFilters"
          >
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { reactive, watch } from 'vue';
  
  const props = defineProps({
    searchQuery: { type: String, default: '' },
    groupFilter: { type: String, default: '' },
    monthFilter: { type: String, default: '' },
    groups: { type: Array, required: true }
  });
  
  const emit = defineEmits(['filter-change']);
  
  // Using a reactive object for all filters
  const filters = reactive({
    searchQuery: props.searchQuery,
    groupFilter: props.groupFilter,
    monthFilter: props.monthFilter
  });
  
  // Watch for all prop changes with a single watcher
  watch(() => ({ 
    search: props.searchQuery, 
    group: props.groupFilter, 
    month: props.monthFilter 
  }), (newVal) => {
    filters.searchQuery = newVal.search;
    filters.groupFilter = newVal.group;
    filters.monthFilter = newVal.month;
  }, { deep: true });
  
  // Emit filter changes to parent
  function emitFilters() {
    emit('filter-change', { ...filters });
  }
  </script>