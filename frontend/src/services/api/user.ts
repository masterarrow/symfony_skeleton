import type { AxiosResponse } from 'axios'
import { http } from '@/services/http'
import type { ApiResponce } from '@/services/api/apiResponce'

export const me = async (): Promise<MeResponse> => {
  const response: AxiosResponse<MeResponse> = await http.get('/users/me')
  return response.data
}

export const getProfile = async (): Promise<UserResponse> => {
  const response: AxiosResponse<UserResponse> = await http.get('/users/profile')
  return response.data
}

export interface UserResponse extends ApiResponce {
  data: {
    message?: string
    user: IUserData
  }
}

interface IUserData {
  id: number
  first_name: string
  last_name: string
  full_name: string
  email: string
  phone: string
  phone_prefix: string
  country: string
}

export interface MeResponse {
  data: {
    roles: string[]
    balance: number
  }
}
