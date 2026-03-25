<template>
    <div class="relative">
      <div class="w-wull mx-auto px-16 bg-gray-50 pt-4 pb-4">
        <nav class="relative flex items-center h-10 justify-center" aria-label="Global">
          <div class="flex items-center flex-1 absolute inset-y-0 left-0">
            <div class="flex w-full items-center justify-between">
              <a href="/">
                <span class="sr-only">Company</span>
                <svg class="logo" viewBox="0 0 128 128" width="24" height="24" data-v-41b4c67b="">
                    <path fill="#42b883" d="M78.8,10L64,35.4L49.2,10H0l64,110l64-110C128,10,78.8,10,78.8,10z" data-v-41b4c67b=""></path>
                    <path fill="#35495e" d="M78.8,10L64,35.4L49.2,10H25.6L64,76l38.4-66H78.8z" data-v-41b4c67b=""></path>
                </svg>
              </a>
            </div>
          </div>
          <div class="flex gap-10">
            <template v-for="(item, i) in navLinks" :key="i">
                <a
                    v-if="!item.auth || (item.auth && authStore.getLoggedIn)"
                    :href="item.href"
                    class="text-gray-600 hover:text-indigo-600 font-medium"
                >
                    {{ item.name }}
                </a>
            </template>
          </div>
          <div class="absolute inset-y-0 right-0 flex items-center justify-end">
            <span class="inline-flex rounded-md shadow">
                <a
                    v-if="!authStore.getLoggedIn && router.currentRoute.value.path !== '/sign-in'"
                    :href="router.resolve({ name: 'sign-in' }).href"
                    class="px-4 py-2 text-base font-medium text-indigo-600 bg-white rounded-md border border-transparent hover:bg-gray-50"
                >
                    Sign in
                </a>
                <a
                    v-if="!authStore.getLoggedIn && router.currentRoute.value.path === '/sign-in'"
                    :href="router.resolve({ name: 'sign-up' }).href"
                    class="px-4 py-2 text-base font-medium text-indigo-600 bg-white rounded-md border border-transparent hover:bg-gray-50"
                >
                    Sign up
                </a>
                <Button
                    v-if="authStore.getLoggedIn"
                    type="button"
                    severity="secondary"
                    @click="logout"
                    label="Log out"
                    class="px-4 py-2 text-base font-medium text-indigo-600 bg-white rounded-md border border-transparent hover:bg-gray-50"
                    :loading="state.loading" />
            </span>
          </div>
        </nav>
      </div>
    </div>
</template>

<script lang="ts" setup>
import { reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '@/stores/useAuth'
import { sendLogout } from '@/services/api/login'
import { useToast } from 'primevue/usetoast';
import Button from 'primevue/button';

const router = useRouter()
const authStore = useAuth()
const state = reactive({ loading: false })
const toast = useToast();

const navLinks = [
  { name: 'Product', href: '#', auth: true },
  { name: 'Features', href: '#', auth: false },
  { name: 'Pricing', href: '#', auth: false },
  { name: 'FAQ', href: '#', auth: false },
  { name: 'Support', href: '#', auth: false },
];

const logout = async () => {
  state.loading = true

  try {
    const res = await sendLogout()

    if (res.status) {
      toast.add({ severity: 'success', summary: 'Successfully logged out', life: 3000 });
      authStore.reset()
      router.push({ name: 'home' })
    }
  } catch (error: any) {
    toast.add({ severity: 'error', summary: 'Something went wrong', life: 3000 });
  } finally {
    state.loading = false
  }
}
</script>
