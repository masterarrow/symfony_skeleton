<template>
    <div class="flex flex-col items-center justify-center h-full gap-8">
        <p class="text-6xl font-extrabold flex flex-col items-center text-gray-900">
            <span class="block">Transform Your Business</span>
            <span class="block text-indigo-600">With Our Platform</span>
        </p>
        <p class="text-xl text-gray-600">
            Streamline operations, gain powerful insights, and accelerate growth with our all-in-one solution.
        </p>
        <div class="mt-5 mx-auto flex justify-center gap-8">
            <div class="shadow rounded-md">
                <a
                    :href="router.resolve({ name: 'sign-in' }).href"
                    class="flex items-center justify-center w-full px-8 py-3 text-base font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700"
                >
                    Get started
                </a>
            </div>
            <div class="mt-3 shadow rounded-md sm:mt-0 sm:ml-3">
                <a
                    href="#"
                    class="flex items-center justify-center w-full px-8 py-3 text-base font-medium text-indigo-600 bg-white border border-transparent rounded-md hover:bg-gray-50"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor" id="Windframe_6jnzZC4Ac">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path>
                    </svg>
                    Watch Demo
                </a>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { onBeforeMount } from 'vue';
import { useRouter } from 'vue-router'
import { useAuth } from '@/stores/useAuth'
import { getProfile } from '@/services/api/user'
import { useToast } from "primevue/usetoast";

const router = useRouter()
const toast = useToast();
const authStore = useAuth()

const loadUser = async () => {
  try {
    const res = await getProfile()

    if (res.status) {
        const user = res.data.user
        authStore.setEmail(user.email)
        authStore.setLoggedIn(true)
        authStore.setFullName(user.full_name)
        authStore.setRoles(user.roles)
    }
  } catch (error: any) {
    toast.add({ severity: 'error', summary: error.data.message, life: 3000 });
  }
}

onBeforeMount(() => {
    loadUser()
})
</script>
