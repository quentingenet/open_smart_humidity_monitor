<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
  title: string
  value: number | string | null
  unit?: string
  alert?: boolean
  icon?: string
  chipText?: string
}>()

const displayValue = computed(() => {
  if (props.alert && props.value !== null && props.value !== undefined) {
    return 'Threshold exceeded'
  }
  const v = props.value != null ? props.value : '—'
  return `${v}${props.unit ? ` ${props.unit}` : ''}`
})
</script>

<template>
  <v-card
    rounded="xl"
    elevation="2"
    class="overflow-hidden"
    :class="{ 'dashboard-card--alert': alert }"
  >
    <v-card-text class="pa-6 text-center">
      <div class="d-flex align-center justify-center gap-4 mb-3 flex-wrap card-header-row">
        <v-icon
          v-if="icon"
          :icon="alert ? 'mdi-alert' : icon"
          size="x-large"
          :color="alert ? 'error' : 'primary'"
          class="align-self-center"
        />
        <span class="text-body-2 text-medium-emphasis align-self-center card-title-text">{{ title }}</span>
        <v-chip v-if="chipText" size="small" color="primary" variant="tonal" prepend-icon="mdi-gauge" class="align-self-center card-chip">
          {{ chipText }}
        </v-chip>
      </div>
      <div
        class="text-h4 font-weight-medium"
        :class="alert ? 'text-error' : 'text-primary'"
      >
        {{ displayValue }}
      </div>
    </v-card-text>
  </v-card>
</template>

<style scoped>
.dashboard-card--alert {
  background: rgba(176, 0, 32, 0.06);
}
.card-header-row .card-title-text {
  margin-left: 4px;
}
.card-header-row .card-chip {
  margin-left: 4px;
}
</style>
