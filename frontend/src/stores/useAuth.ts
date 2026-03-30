import { defineStore } from 'pinia'
import { useStorage } from '@vueuse/core'

export const useAuth = defineStore('auth', {
  state: () => ({
    email: useStorage('auth.email', ''),
    fullName: useStorage('auth.fullName', ''),
    error: useStorage('auth.error', ''),
    loggedIn: useStorage('auth.loggedIn', false),
    roles: useStorage('auth.roles', ['']),
    balance: useStorage('auth.balance', 0),
  }),
  actions: {
    setEmail(email: string): void {
      this.email = email
    },
    setFullName(fullName: string): void {
      this.fullName = fullName
    },
    setRoles(roles: string[]): void {
      this.roles = roles
    },
    setBalance(balance: number): void {
      this.balance = balance
    },
    setError(err: string): void {
      this.error = err
      this.loggedIn = false
    },
    setLoggedIn(loggedIn: boolean): void {
      this.loggedIn = loggedIn
    },
    reset(): void {
      this.email = ''
      this.fullName = ''
      this.error = ''
      this.loggedIn = false
      this.roles = ['']
    },
  },
  getters: {
    getEmail: (state) => state.email,
    getFullName: (state) => state.fullName,
    getError: (state) => state.error,
    getLoggedIn: (state) => state.loggedIn,
    getRoles: (state) => state.roles,
    getBalance: (state) => state.balance
  },
})
