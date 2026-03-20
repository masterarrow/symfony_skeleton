import './assets/main.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router'
import http from './services/http'
import PrimeVue from 'primevue/config';
import ToastService from 'primevue/toastservice'
import Vue3Toastify, { type ToastContainerOptions } from 'vue3-toastify';
import Aura from '@primeuix/themes/aura';
import App from './App.vue'
import './assets/main.css'

const app = createApp(App)

app.use(createPinia())
app.use(router)
app.use(http)
app.use(PrimeVue, {
    theme: {
        preset: Aura,
        options: {
            darkModeSelector: false,
            cssLayer: {
                name: 'primevue',
                order: 'theme, base, primevue'
            },
            ripple: true,
            inputVariant: 'filled'
        }
    }
})
app.use(ToastService);
app.use(Vue3Toastify, {
  autoClose: 3000,
} as ToastContainerOptions);

app.mount('#app')
