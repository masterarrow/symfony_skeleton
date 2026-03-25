import type { AxiosResponse } from 'axios'
import { http } from '@/services/http'

export const sendRegisterForm = async (data: RegisterData): Promise<RegisterResponse> => {
  const response: AxiosResponse<RegisterResponse> = await http.post('/users/register', data)
  return response.data
}

export interface RegisterResponse {
  status: boolean
  data: {
    message?: string
    error?: string
  }
}

export interface RegisterData {
    first_name: string
    last_name: string
    email: string
    country: string
    phone_prefix: string
    phone: string
    password: string
}
