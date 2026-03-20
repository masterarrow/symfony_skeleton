import type { App } from 'vue'
import axios, { type AxiosInstance } from 'axios'
import { useAuth } from '@/stores/useAuth'
import router from '../router'
import { toast } from 'vue3-toastify'

let logoutTimer: ReturnType<typeof setTimeout> | null = null

const setSessionTimeout = (minutes = 120) => {
  if (logoutTimer) {
    clearTimeout(logoutTimer)
  }

  logoutTimer = setTimeout(
    () => {
      handleSessionExpire()
    },
    minutes * 60 * 1000,
  )
}

const handleSessionExpire = () => {
  const authStore = useAuth()
  if (authStore.getLoggedIn) {
    toast.error('Session expired. Please login again.')
  }
  authStore.reset()

  router.push({ name: 'login' })
}

const http: AxiosInstance = axios.create({
  baseURL: '/api',
  withCredentials: true,
  headers: {
    'Content-Type': 'application/json',
  },
})

const setupInterceptors = () => {
  http.interceptors.request.use(
    (response) => {
      setSessionTimeout()

      return response
    },
    (error) => Promise.reject(error),
  )

  http.interceptors.response.use(
    (response) => response,
    async (error) => {
      if (error.response && error.response.status === 419) {
        return http(error.config)
      }

      if (error.response && error.response.status === 401) {
        console.error('Unauthorized access')
        handleSessionExpire()

        return Promise.reject()
      }
      return Promise.reject(error)
    },
  )
}

export default {
  install(app: App) {
    setupInterceptors()
    app.config.globalProperties.$http = http
  },
}

export { http }
