<template>
  <div class="login-wrapper">
    <div class="login-card">
      <div class="brand-header">
        <div class="brand-icon">
          📦
        </div>
        <h2>{{ isRegister ? 'Daftar Akun Baru' : 'Login Sistem Produk' }}</h2>
        <p class="subtitle">
          {{ isRegister ? 'Buat akun untuk mengakses katalog produk' : 'Gunakan kredensial Anda untuk masuk' }}
        </p>
      </div>

      <div
        v-if="errorMessage"
        class="alert alert-danger"
      >
        ⚠️ {{ errorMessage }}
      </div>

      <div
        v-if="successMessage"
        class="alert alert-success"
      >
        ✅ {{ successMessage }}
      </div>

      <form @submit.prevent="handleSubmit">
        <div
          v-if="isRegister"
          class="form-group"
        >
          <label>Nama Lengkap</label>
          <input
            v-model="form.name"
            type="text"
            placeholder="Contoh: Hisyam Alrayyan"
            required
            class="form-control"
          >
        </div>

        <div class="form-group">
          <label>Alamat Email</label>
          <input
            v-model="form.email"
            type="email"
            placeholder="contoh@domain.com"
            required
            class="form-control"
          >
        </div>

        <div class="form-group">
          <label>Kata Sandi (Password)</label>
          <input
            v-model="form.password"
            type="password"
            placeholder="Minimal 8 karakter"
            required
            class="form-control"
          >
        </div>

        <div
          v-if="isRegister"
          class="form-group"
        >
          <label>Konfirmasi Kata Sandi</label>
          <input
            v-model="form.password_confirmation"
            type="password"
            placeholder="Ulangi kata sandi"
            required
            class="form-control"
          >
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="btn btn-primary btn-block"
        >
          <span v-if="loading">Memproses...</span>
          <span v-else>{{ isRegister ? 'Daftar Sekarang' : 'Masuk (Login)' }}</span>
        </button>
      </form>

      <div class="toggle-mode">
        <span>{{ isRegister ? 'Sudah memiliki akun?' : 'Belum memiliki akun?' }}</span>
        <button
          type="button"
          class="btn-link"
          @click="toggleMode"
        >
          {{ isRegister ? 'Login di sini' : 'Daftar akun baru' }}
        </button>
      </div>

      <div class="demo-credentials">
        <small>💡 <strong>Default Credential:</strong> hisyam@example.com / password123</small>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import apiClient from '../api/axios'

const router = useRouter()
const isRegister = ref(false)
const loading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

const form = reactive({
  name: '',
  email: 'hisyam@example.com',
  password: 'password123',
  password_confirmation: '',
})

const toggleMode = () => {
  isRegister.value = !isRegister.value
  errorMessage.value = ''
  successMessage.value = ''
}

const handleSubmit = async () => {
  loading.value = true
  errorMessage.value = ''
  successMessage.value = ''

  try {
    if (isRegister.value) {
      const response = await apiClient.post('/api/register', {
        name: form.name,
        email: form.email,
        password: form.password,
        password_confirmation: form.password_confirmation,
      })

      // Simpan token ke localStorage sesuai ketentuan Soal 2a
      localStorage.setItem('token', response.data.token)
      localStorage.setItem('user', JSON.stringify(response.data.user))

      successMessage.value = 'Pendaftaran berhasil! Mengalihkan ke dashboard...'
      setTimeout(() => router.push('/'), 800)
    } else {
      const response = await apiClient.post('/api/login', {
        email: form.email,
        password: form.password,
      })

      // Simpan token ke localStorage sesuai ketentuan Soal 2a
      localStorage.setItem('token', response.data.token)
      localStorage.setItem('user', JSON.stringify(response.data.user))

      successMessage.value = 'Login berhasil! Mengalihkan ke dashboard...'
      setTimeout(() => router.push('/'), 600)
    }
  } catch (err) {
    if (err.response && err.response.data && err.response.data.message) {
      errorMessage.value = err.response.data.message
    } else {
      errorMessage.value = 'Koneksi ke server gagal. Pastikan backend Laravel berjalan.'
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.login-wrapper {
  min-height: calc(100vh - 70px);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px;
}

.login-card {
  width: 100%;
  max-width: 440px;
  background: #ffffff;
  border-radius: 12px;
  padding: 32px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
  border: 1px solid #e5e7eb;
}

.brand-header {
  text-align: center;
  margin-bottom: 24px;
}

.brand-icon {
  font-size: 2.5rem;
  margin-bottom: 8px;
}

.brand-header h2 {
  font-size: 1.4rem;
  color: #1f2937;
  font-weight: 700;
}

.subtitle {
  color: #6b7280;
  font-size: 0.9rem;
  margin-top: 4px;
}

.form-group {
  margin-bottom: 18px;
}

.form-group label {
  display: block;
  font-size: 0.85rem;
  font-weight: 600;
  color: #374151;
  margin-bottom: 6px;
}

.form-control {
  width: 100%;
  padding: 10px 14px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 0.95rem;
  transition: border-color 0.2s, box-shadow 0.2s;
  outline: none;
}

.form-control:focus {
  border-color: #2563eb;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
}

.btn {
  padding: 11px 16px;
  border-radius: 6px;
  font-weight: 600;
  font-size: 0.95rem;
  cursor: pointer;
  border: none;
  transition: background-color 0.2s;
}

.btn-primary {
  background-color: #2563eb;
  color: #ffffff;
}

.btn-primary:hover:not(:disabled) {
  background-color: #1d4ed8;
}

.btn-primary:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.btn-block {
  width: 100%;
}

.alert {
  padding: 12px 14px;
  border-radius: 6px;
  font-size: 0.88rem;
  margin-bottom: 18px;
}

.alert-danger {
  background-color: #fee2e2;
  color: #b91c1c;
  border: 1px solid #fca5a5;
}

.alert-success {
  background-color: #dcfce7;
  color: #15803d;
  border: 1px solid #86efac;
}

.toggle-mode {
  margin-top: 20px;
  text-align: center;
  font-size: 0.88rem;
  color: #6b7280;
}

.btn-link {
  background: none;
  border: none;
  color: #2563eb;
  font-weight: 600;
  cursor: pointer;
  margin-left: 6px;
  padding: 0;
  text-decoration: underline;
}

.demo-credentials {
  margin-top: 20px;
  padding-top: 16px;
  border-top: 1px dashed #e5e7eb;
  text-align: center;
  color: #6b7280;
  font-size: 0.8rem;
}
</style>
