import axios, { type AxiosInstance } from 'axios';

export default defineNuxtPlugin((nuxtApp) => {
  const config = useRuntimeConfig()

  const api: AxiosInstance = axios.create({
    baseURL: config.public.apiBase as string,
    withCredentials: true,
  })

  const csrfApi: AxiosInstance = axios.create({
    baseURL: config.public.apiBase as string,
    withCredentials: true,
  })

  // Interceptor para garantir CSRF em todas as requisições
  api.interceptors.request.use(async (cfg) => {
    try {
      await csrfApi.get('/sanctum/csrf-cookie')
      
      // Get the CSRF token from cookies and add it to headers
      const cookies = document.cookie.split(';')
      for (const cookie of cookies) {
        const [name, value] = cookie.trim().split('=')
        if (name === 'XSRF-TOKEN' && value) {
          cfg.headers['X-XSRF-TOKEN'] = decodeURIComponent(value)
          break
        }
      }
    } catch (error) {
      console.warn('Failed to get CSRF cookie:', error)
    }
    return cfg
  })

  nuxtApp.provide('api', api)
})