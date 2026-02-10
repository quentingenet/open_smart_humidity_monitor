<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { fetchSensorSummary } from '@/api/sensorApi'
import type { SensorSummary } from '@/types/sensor'
import DashboardCard from '@/components/DashboardCard.vue'
import HumidityChart from '@/components/HumidityChart.vue'

const sensorId = ref(1)
const data = ref<SensorSummary | null>(null)
const loading = ref(true)
const error = ref<string | null>(null)
const offline = ref(false)

const demoMode = import.meta.env.VITE_DEMO_MODE === 'true'

const demoData: SensorSummary = {
  latestHumidity: 62,
  average7Days: 58.5,
  isAlert: false,
  humidityThreshold: 70,
}

onMounted(async () => {
  loading.value = true
  error.value = null
  offline.value = false
  try {
    data.value = await fetchSensorSummary(sensorId.value)
  } catch (e) {
    if (demoMode) {
      offline.value = true
      data.value = demoData
    } else {
      error.value = e instanceof Error ? e.message : 'API error'
    }
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <v-app-bar flat color="primary" rounded="0" class="justify-center">
    <v-app-bar-title class="text-h6 font-weight-medium text-white">
      Open Smart Humidity Monitor
    </v-app-bar-title>
  </v-app-bar>

  <v-main class="dashboard-bg">
    <v-container class="py-6">
      <v-progress-linear v-if="loading" indeterminate color="primary" class="mb-4 rounded-xl" />
      <v-alert
        v-if="offline && data && demoMode"
        type="warning"
        density="compact"
        class="mb-4 rounded-xl"
        variant="tonal"
      >
        API unavailable — displaying demo data.
      </v-alert>
      <v-alert
        v-else-if="error && !data"
        type="error"
        dismissible
        class="mb-4 rounded-xl"
      >
        {{ error }}
      </v-alert>

      <template v-if="data">
        <v-row class="mb-6">
          <v-col cols="12" sm="6" md="4">
            <DashboardCard
              title="Current humidity"
              :value="data.latestHumidity"
              unit="%"
              :alert="data.isAlert"
              icon="mdi-water-percent"
            />
          </v-col>
          <v-col cols="12" sm="6" md="4">
            <DashboardCard
              title="7-day average"
              :value="data.average7Days"
              unit="%"
              icon="mdi-chart-line"
            />
          </v-col>
          <v-col cols="12" sm="6" md="4">
            <v-tooltip location="top" :text="`Determined threshold: ${data.humidityThreshold} %`">
              <template #activator="{ props: tooltipProps }">
                <div v-bind="tooltipProps">
                  <DashboardCard
                    title="Alert"
                    :value="data.isAlert ? 'Yes' : 'No'"
                    :alert="data.isAlert"
                    icon="mdi-alert-circle-outline"
                    :chip-text="`Threshold: ${data.humidityThreshold} %`"
                  />
                </div>
              </template>
            </v-tooltip>
          </v-col>
        </v-row>

        <v-row>
          <v-col cols="12">
            <HumidityChart :labels="['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']" :values="[52, 58, 55, 62, 59, 61, 58]" />
          </v-col>
        </v-row>
      </template>
    </v-container>
  </v-main>
</template>

<style scoped>
.dashboard-bg {
  background-color: #ECEFF1;
  min-height: 100vh;
}
</style>
