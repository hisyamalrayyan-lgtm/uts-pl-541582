<template>
  <div id="app">
    <header class="navbar">
      <div class="nav-container">
        <div class="nav-brand">
          <router-link
            to="/"
            class="brand-link"
          >
            📦 <span class="brand-title">Katalog Produk UTS</span>
          </router-link>
        </div>

        <nav class="nav-links">
          <router-link
            v-if="isAuthenticated"
            to="/"
            class="nav-link"
            active-class="active"
          >
            Katalog Produk
          </router-link>
          <router-link
            to="/about"
            class="nav-link"
            active-class="active"
          >
            Tentang
          </router-link>
        </nav>

        <div class="nav-auth">
          <template v-if="isAuthenticated">
            <span class="user-greeting">
              👤 <strong>{{ currentUser?.name || 'User' }}</strong>
            </span>
            <button
              class="btn-logout"
              @click="handleLogout"
            >
              Keluar
            </button>
          </template>
          <template v-else>
            <router-link
              to="/login"
              class="btn-login"
            >
              Masuk
            </router-link>
          </template>
        </div>
      </div>
    </header>

    <main class="main-content">
      <router-view />
    </main>

    <footer class="footer">
      <p>&copy; 2026 UTS Konstruksi dan Evolusi Perangkat Lunak (SVPL26150) &bull; Hisyam Alrayyan (541582)</p>
    </footer>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import apiClient from './api/axios'

const router = useRouter()
const route = useRoute()

const token = ref(localStorage.getItem('token'))
const userStr = ref(localStorage.getItem('user'))

const isAuthenticated = computed(() => !!token.value)
const currentUser = computed(() => {
  try {
    return userStr.value ? JSON.parse(userStr.value) : null
  } catch {
    return null
  }
})

// Update auth state on route changes
watch(route, () => {
  token.value = localStorage.getItem('token')
  userStr.value = localStorage.getItem('user')
})

const handleLogout = async () => {
  try {
    await apiClient.post('/api/logout')
  } catch {
    // Abaikan jika network error, tetap bersihkan token lokal
  } finally {
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    token.value = null
    userStr.value = null
    router.push('/login')
  }
}

onMounted(() => {
  token.value = localStorage.getItem('token')
  userStr.value = localStorage.getItem('user')
})
</script>

<style>
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
  background-color: #f8fafc;
  color: #1e293b;
  min-height: 100vh;
}

#app {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.navbar {
  background-color: #1e293b;
  color: #ffffff;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  position: sticky;
  top: 0;
  z-index: 100;
}

.nav-container {
  max-width: 1100px;
  margin: 0 auto;
  padding: 12px 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.brand-link {
  color: #ffffff;
  text-decoration: none;
  font-size: 1.15rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  gap: 8px;
}

.brand-title {
  letter-spacing: -0.3px;
}

.nav-links {
  display: flex;
  gap: 16px;
}

.nav-link {
  color: #94a3b8;
  text-decoration: none;
  font-size: 0.92rem;
  font-weight: 500;
  padding: 6px 12px;
  border-radius: 6px;
  transition: all 0.2s;
}

.nav-link:hover {
  color: #ffffff;
  background-color: #334155;
}

.nav-link.active {
  color: #ffffff;
  background-color: #2563eb;
}

.nav-auth {
  display: flex;
  align-items: center;
  gap: 14px;
}

.user-greeting {
  font-size: 0.88rem;
  color: #cbd5e1;
}

.btn-logout {
  background: none;
  border: 1px solid #475569;
  color: #cbd5e1;
  padding: 6px 14px;
  border-radius: 6px;
  font-size: 0.85rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-logout:hover {
  background-color: #dc2626;
  border-color: #dc2626;
  color: #ffffff;
}

.btn-login {
  background-color: #2563eb;
  color: #ffffff;
  text-decoration: none;
  padding: 6px 16px;
  border-radius: 6px;
  font-size: 0.88rem;
  font-weight: 600;
}

.main-content {
  flex: 1;
}

.footer {
  background-color: #ffffff;
  border-top: 1px solid #e2e8f0;
  padding: 20px;
  text-align: center;
  font-size: 0.85rem;
  color: #64748b;
  margin-top: 40px;
}
</style>
