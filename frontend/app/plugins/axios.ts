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
    await csrfApi.get('/sanctum/csrf-cookie')
    return cfg
  })

  nuxtApp.provide('api', api)
})