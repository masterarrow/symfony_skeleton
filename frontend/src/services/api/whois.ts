import type { AxiosResponse } from 'axios'
import { http } from '@/services/http'
import type { ApiResponce } from '@/services/api/apiResponce'

export const domainWhois = async (domain: string): Promise<WhoisResponse> => {
  const response: AxiosResponse<WhoisResponse> = await http.get(`/domains/whois/${domain}`)
  return response.data
}

export interface WhoisResponse extends ApiResponce {
  data: {
    domain: string
    available: boolean
    registrar: string
    registrar_url: string
    registrant_email: string
    registrar_abuse: string
    name_servers: string[]
    dnssec: string
    whois_server: string
    cr_date: string
    updated_date: string
    exp_date: string
    owner: string
    states: string[]
  }
}
