<template>
  <div class="bg-white p-6 rounded-lg shadow-sm h-[400px]">
    <h3 class="text-lg font-semibold mb-4">{{ title }}</h3>
    <div class="h-[calc(100%-2rem)]">
      <canvas :id="chartId" ref="chartRef"></canvas>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch, defineProps } from 'vue'
import { Chart } from 'chart.js/auto'

const props = defineProps({
  chartId: {
    type: String,
    required: true
  },
  title: {
    type: String,
    required: true
  },
  type: {
    type: String,
    required: true,
    validator: (value) => ['line', 'bar', 'pie', 'doughnut'].includes(value)
  },
  data: {
    type: Object,
    required: true,
    validator: (value) => value.labels && value.datasets
  }
})

const chartRef = ref(null)
let chart = null

const createChart = () => {
  if (chart) {
    chart.destroy()
  }

  const ctx = chartRef.value.getContext('2d')
  chart = new Chart(ctx, {
    type: props.type,
    data: props.data,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom'
        }
      }
    }
  })
}

onMounted(() => {
  createChart()
})

watch(
  () => props.data,
  () => {
    createChart()
  },
  { deep: true }
)
</script>

<style scoped>
canvas {
  height: 100% !important;
  width: 100% !important;
}
</style>