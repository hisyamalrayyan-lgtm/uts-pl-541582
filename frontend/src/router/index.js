import { createRouter, createWebHistory } from 'vue-router'
import ProdukView from '../views/ProdukView.vue'
import LoginView from '../views/LoginView.vue'
import AboutView from '../views/AboutView.vue'

const routes = [
  {
    path: '/',
    name: 'Produk',
    component: ProdukView,
    meta: { requiresAuth: true },
  },
  {
    path: '/login',
    name: 'Login',
    component: LoginView,
  },
  {
    path: '/about',
    name: 'About',
    component: AboutView,
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

// Navigation Guard: Lindungi route yang membutuhkan autentikasi token Sanctum (Soal 2a)
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')

  if (to.meta.requiresAuth && !token) {
    next({ name: 'Login' })
  } else if (to.name === 'Login' && token) {
    next({ name: 'Produk' })
  } else {
    next()
  }
})

export default router
