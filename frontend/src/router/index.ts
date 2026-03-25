import { createRouter, createWebHistory } from 'vue-router'
import { useAuth } from '@/stores/useAuth'
import { me } from '@/services/api/user'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/:catchAll(.*)',
      name: '404',
      component: () => import('@/components/AppNotFound.vue'),
      meta: { requiresAuth: false },
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: () => import('@/pages/Dashboard/DashboardPage.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/',
      name: 'home',
      component: () => import('@/pages/Home/HomePage.vue'),
      meta: { requiresAuth: false, guestOnly: true },
    },
    {
      path: '/sign-in',
      name: 'sign-in',
      component: () => import('@/pages/Login/LoginPage.vue'),
      meta: { requiresAuth: false, guestOnly: true },
    },
    {
      path: '/sign-up',
      name: 'sign-up',
      component: () => import('@/pages/Register/RegisterPage.vue'),
      meta: { requiresAuth: false, guestOnly: true },
    },
  ],
})

router.beforeEach(async (to, from, next) => {
  const authStore = useAuth()
  try {
    await me()
  } catch (error: any) {
    authStore.reset()
  }

  const isLoggedIn = authStore.getLoggedIn

  if (to.meta.requiresAuth && !isLoggedIn) {
    next({ name: 'home' })
  } else if (to.meta.guestOnly && isLoggedIn) {
    next({ name: 'dashboard' })
  } else {
    next()
  }
})

export default router
