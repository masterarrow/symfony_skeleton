export interface ApiResponce {
    status: boolean
    error?: string
    errors?: Record<string, string|string[]>
    data?: Record<string, any>
}
