import type { AxiosResponse } from 'axios'
import { http } from '@/services/http'
import type { ApiResponce } from '@/services/api/apiResponce'

export const sendRegisterForm = async (data: RegisterData): Promise<RegisterResponse> => {
  const response: AxiosResponse<RegisterResponse> = await http.post('/users/create', data)
  return response.data
}

export interface RegisterResponse extends ApiResponce {
  data: {
    message?: string
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
