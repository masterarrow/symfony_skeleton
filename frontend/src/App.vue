<template>
    <Toast />

    <div class="flex flex-col min-h-screen">
        <AppNavbar />
        <div class="flex-1 w-full p-16">
            <RouterView />
        </div>
        <AppFooter />
    </div>
</template>

<script lang="ts" setup>
import { RouterView } from 'vue-router'
import Toast from 'primevue/toast'

import AppNavbar from '@/components/AppNavbar.vue'
import AppFooter from '@/components/AppFooter.vue'
</script>
