import type { AxiosResponse } from 'axios'
import { http } from '@/services/http'

export const sendLoginForm = async (data: LoginData): Promise<LoginResponse> => {
  const response: AxiosResponse<LoginResponse> = await http.post('/users/login', data)
  return response.data
}

export const sendLogout = async (): Promise<{ status: boolean }> => {
  const response: AxiosResponse<{ status: boolean }> = await http.post('/users/logout')
  return response.data
}

export interface LoginResponse {
  status: boolean
  data: {
    message: string
    error: string
    user: LoginUser
  }
}

export interface LoginData {
  email: string
  password: string
}

export interface LoginUser {
  id: number
  full_name: string
  email: string
}
