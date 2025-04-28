<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Expenses</h1>
      <button @click="openExpenseModal" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
        Add Expense
      </button>
    </div>

    <!-- Export Expenses -->
    <div class="flex gap-3 mb-4 justify-end">
      <button @click="exportToCSV()"
        class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 flex items-center">
        <span class="mr-1">Export to CSV</span>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M12 10v6m0 0l-3-3m3 3l3-3m-9 0H3.6a1.2 1.2 0 01-1.2-1.2V4.2A1.2 1.2 0 013.6 3h16.8a1.2 1.2 0 011.2 1.2v12.6a1.2 1.2 0 01-1.2 1.2H12" />
        </svg>
      </button>
      <button @click="exportToPDF()" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 flex items-center">
        <span class="mr-1">Export to PDF</span>
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
const { jsPDF } = window.jspdf;

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

function exportToCSV() {
  // Map expenses to include group names
  const exportData = expenseStore.expenses.map(expense => {
    const group = groupStore.groups.find(g => g.id === expense.group_id);
    return {
      name: expense.name,
      amount: expense.amount,
      date: expense.date,
      group: group ? group.name : 'No Group'
    };
  });
  const csv = Papa.unparse(exportData);
  const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = 'expenses.csv';
  link.click();
}

function exportToPDF() {
  const doc = new jsPDF();
  const pageWidth = doc.internal.pageSize.width;

  // Add title
  doc.setFontSize(20);
  doc.setFont('helvetica', 'bold');
  doc.text('Expense Tracker Report', pageWidth / 2, 20, { align: 'center' });

  // Add date
  doc.setFontSize(10);
  doc.setFont('helvetica', 'normal');
  doc.text(`Generated on: ${moment().format('MMMM D, YYYY')}`, pageWidth / 2, 30, { align: 'center' });

  // Table configuration
  const startY = 40;
  const margin = 10;
  const cellPadding = 2;
  const columns = [
    { header: 'Date', width: 30 },
    { header: 'Name', width: 60 },
    { header: 'Amount', width: 30, align: 'right' },
    { header: 'Group', width: 50 }
  ];

  // Draw table header
  let currentX = margin;
  let currentY = startY;

  // Header background
  doc.setFillColor(240, 240, 240);
  doc.rect(margin, currentY, pageWidth - (margin * 2), 10, 'F');

  // Header text
  doc.setFont('helvetica', 'bold');
  doc.setFontSize(11);
  columns.forEach(col => {
    doc.text(col.header, currentX + cellPadding, currentY + 7);
    currentX += col.width;
  });

  // Table content
  currentY += 10;
  doc.setFont('helvetica', 'normal');
  doc.setFontSize(10);

  let totalAmount = 0;

  expenseStore.expenses.forEach((expense) => {
    const group = groupStore.groups.find(g => g.id === expense.group_id);
    const groupName = group ? group.name : 'No Group';
    currentX = margin;

    // Add new page if needed
    if (currentY > 270) {
      doc.addPage();
      currentY = margin + 10;

      // Draw header on new page
      doc.setFillColor(240, 240, 240);
      doc.rect(margin, margin, pageWidth - (margin * 2), 10, 'F');
      doc.setFont('helvetica', 'bold');
      let headerX = margin;
      columns.forEach(col => {
        doc.text(col.header, headerX + cellPadding, margin + 7);
        headerX += col.width;
      });
      doc.setFont('helvetica', 'normal');
    }

    // Draw row
    doc.text(moment(expense.date).format('YYYY-MM-DD'), currentX + cellPadding, currentY + 5);
    currentX += columns[0].width;

    doc.text(expense.name, currentX + cellPadding, currentY + 5);
    currentX += columns[1].width;

    const amount = Number(expense.amount);
    totalAmount += amount;
    doc.text(`₹${amount.toFixed(2)}`, currentX + columns[2].width - cellPadding, currentY + 5, { align: 'right' });
    currentX += columns[2].width;

    doc.text(groupName, currentX + cellPadding, currentY + 5);

    // Draw horizontal line
    doc.setDrawColor(200, 200, 200);
    doc.line(margin, currentY, pageWidth - margin, currentY);

    currentY += 8;
  });

  // Draw total
  currentY += 5;
  doc.setFont('helvetica', 'bold');
  doc.text('Total:', margin + columns[0].width + columns[1].width, currentY + 5);
  doc.text(`₹${totalAmount.toFixed(2)}`, margin + columns[0].width + columns[1].width + columns[2].width - cellPadding, currentY + 5, { align: 'right' });

  // Add footer
  const pageCount = doc.internal.getNumberOfPages();
  doc.setFont('helvetica', 'italic');
  doc.setFontSize(8);
  for (let i = 1; i <= pageCount; i++) {
    doc.setPage(i);
    doc.text(`Page ${i} of ${pageCount}`, pageWidth / 2, 290, { align: 'center' });
  }
  doc.save('expenses.pdf');
}

onMounted(async () => {
  await expenseStore.fetchExpenses();
  await groupStore.fetchGroups();
  applyFilters();
})
</script>