import ky from 'ky'

const baseURL = import.meta.env.VITE_API_BASE_URL ?? 'http://127.0.0.1:8000/api'

export const httpClient = ky.create({
  prefixUrl: baseURL,
  timeout: 5000,
  retry: 1,
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
})
