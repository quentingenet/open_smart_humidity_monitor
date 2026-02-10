import { httpClient } from './httpClient'
import type { SensorSummary } from '@/types/sensor'

export async function fetchSensorSummary(sensorId: number): Promise<SensorSummary> {
  const response = await httpClient
    .get(`sensors/${sensorId}/summary`)
    .json<SensorSummary>()
  return response
}
