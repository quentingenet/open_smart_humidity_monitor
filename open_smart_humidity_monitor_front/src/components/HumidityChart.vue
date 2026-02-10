<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Filler,
  Title,
  Tooltip,
  Legend,
} from 'chart.js'
import type { ChartData, ChartOptions } from 'chart.js'

const props = withDefaults(
  defineProps<{
    labels?: string[]
    values?: number[]
  }>(),
  {
    labels: () => [],
    values: () => [],
  }
)

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Filler,
  Title,
  Tooltip,
  Legend
)

const canvasEl = ref<HTMLCanvasElement | null>(null)
let chart: ChartJS | null = null

const blue = 'rgba(25, 118, 210, 0.8)'
const blueLight = 'rgba(25, 118, 210, 0.15)'
const grey = 'rgba(120, 144, 156, 0.6)'
const gridColor = 'rgba(120, 144, 156, 0.15)'

function buildGradient(ctx: CanvasRenderingContext2D) {
  const g = ctx.createLinearGradient(0, 0, 0, 300)
  g.addColorStop(0, 'rgba(25, 118, 210, 0.35)')
  g.addColorStop(1, 'rgba(25, 118, 210, 0)')
  return g
}

function initChart() {
  if (!canvasEl.value) return
  const ctx = canvasEl.value.getContext('2d')
  if (!ctx) return

  if (chart) chart.destroy()

  const defaultLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
  const defaultValues = [52, 58, 55, 62, 59, 61, 58]
  const data: ChartData<'line'> = {
    labels: props.labels.length ? props.labels : defaultLabels,
    datasets: [
      {
        label: 'Humidity %',
        data: props.values.length ? props.values : defaultValues,
        borderColor: blue,
        backgroundColor: buildGradient(ctx),
        fill: true,
        tension: 0.35,
        pointRadius: 4,
        pointBackgroundColor: blue,
        pointBorderColor: '#fff',
        pointBorderWidth: 1,
        borderWidth: 2,
      },
    ],
  }

  const options: ChartOptions<'line'> = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { display: false },
      tooltip: {
        backgroundColor: 'rgba(55, 71, 79, 0.9)',
        cornerRadius: 8,
        padding: 12,
      },
    },
    scales: {
      x: {
        grid: { color: gridColor, drawBorder: false },
        ticks: { color: grey, font: { size: 11 } },
      },
      y: {
        min: 0,
        max: 100,
        grid: { color: gridColor, drawBorder: false },
        ticks: { color: grey, font: { size: 11 } },
      },
    },
  }

  chart = new ChartJS(ctx, { type: 'line', data, options })
}

onMounted(initChart)
watch(() => [props.labels, props.values], initChart, { deep: true })
</script>

<template>
  <v-card rounded="xl" elevation="2" class="overflow-hidden chart-card">
    <v-card-title class="text-body-1 font-weight-medium text-primary pa-5 pb-3 d-flex align-center flex-wrap gap-4 chart-title-row">
      <span>Humidity history — Last 7 days</span>
      <v-chip size="small" color="primary" variant="flat" class="ms-1">7d</v-chip>
      <v-chip size="small" variant="tonal" disabled class="text-disabled">30d</v-chip>
    </v-card-title>
    <v-card-text class="pa-4 pt-0">
      <div class="chart-wrap">
        <canvas ref="canvasEl"></canvas>
      </div>
      <p class="text-caption text-medium-emphasis text-center mt-3 mb-0">
        Humidity history will appear here once data is available.
      </p>
    </v-card-text>
  </v-card>
</template>

<style scoped>
.chart-card {
  background: linear-gradient(180deg, #ffffff 0%, #fafafa 100%);
}
.chart-wrap {
  position: relative;
  width: 100%;
  height: 280px;
  border-radius: 16px;
  overflow: hidden;
  background: #fafafa;
}
</style>
