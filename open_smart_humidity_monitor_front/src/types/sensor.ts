export interface SensorSummary {
  latestHumidity: number | string | null
  average7Days: number | null
  isAlert: boolean
  humidityThreshold: number
}
