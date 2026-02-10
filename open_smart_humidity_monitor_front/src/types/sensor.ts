export interface SensorSummary {
  latestHumidity: number | null
  average7Days: number | null
  isAlert: boolean
  humidityThreshold: number
}
