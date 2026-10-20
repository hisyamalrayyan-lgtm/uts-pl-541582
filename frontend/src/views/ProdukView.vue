<template>
  <div class="container">
    <!-- Header Section -->
    <div class="page-header">
      <div>
        <h1 class="page-title">
          📦 Manajemen Katalog Produk
        </h1>
        <p class="page-subtitle">
          Kelola inventaris barang secara terpusat melalui RESTful API
        </p>
      </div>
      <button
        class="btn btn-primary"
        @click="openModal('create')"
      >
        ➕ Tambah Produk
      </button>
    </div>

    <!-- Alert Notifications -->
    <div
      v-if="successMessage"
      class="alert alert-success"
    >
      ✅ {{ successMessage }}
    </div>
    <div
      v-if="errorMessage"
      class="alert alert-danger"
    >
      ⚠️ {{ errorMessage }}
    </div>

    <!-- Stats & Search Toolbar -->
    <div class="toolbar">
      <div class="search-box">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Cari nama atau kategori produk..."
          class="form-control"
        >
      </div>
      <div class="stats-badge">
        Total Produk: <strong>{{ filteredProduks.length }}</strong>
      </div>
    </div>

    <!-- Loading State -->
    <div
      v-if="loading"
      class="empty-state"
    >
      <div class="spinner" />
      <p>Memuat data produk dari backend Laravel...</p>
    </div>

    <!-- Empty State -->
    <div
      v-else-if="filteredProduks.length === 0"
      class="empty-state"
    >
      <p style="font-size: 1.2rem; color: #4b5563;">
        Tidak ada data produk yang ditemukan.
      </p>
      <p style="font-size: 0.88rem; color: #9ca3af; margin-top: 4px;">
        Silakan klik tombol "Tambah Produk" untuk menambahkan barang baru.
      </p>
    </div>

    <!-- Product Table -->
    <div
      v-else
      class="table-card"
    >
      <table class="data-table">
        <thead>
          <tr>
            <th style="width: 60px;">
              ID
            </th>
            <th>Nama Produk</th>
            <th>Kategori</th>
            <th>Harga (Rp)</th>
            <th>Stok</th>
            <th>Deskripsi</th>
            <th style="width: 140px; text-align: center;">
              Aksi
            </th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="item in filteredProduks"
            :key="item.id"
          >
            <td class="text-muted">
              #{{ item.id }}
            </td>
            <td class="font-bold">
              {{ item.nama }}
            </td>
            <td>
              <span class="badge badge-category">{{ item.kategori }}</span>
            </td>
            <td class="font-bold text-success">
              {{ formatCurrency(item.harga) }}
            </td>
            <td>
              <span
                class="badge"
                :class="item.stok > 5 ? 'badge-in-stock' : 'badge-low-stock'"
              >
                {{ item.stok }} unit
              </span>
            </td>
            <td class="text-desc">
              {{ item.deskripsi || '-' }}
            </td>
            <td class="action-cell">
              <button
                class="btn-action btn-edit"
                title="Ubah Data"
                @click="openModal('edit', item)"
              >
                ✏️
              </button>
              <button
                class="btn-action btn-delete"
                title="Hapus Data"
                @click="confirmDelete(item)"
              >
                🗑️
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal Form (Tambah / Ubah) -->
    <div
      v-if="showModal"
      class="modal-backdrop"
      @click.self="closeModal"
    >
      <div class="modal-card">
        <div class="modal-header">
          <h3>{{ modalMode === 'create' ? 'Tambah Produk Baru' : 'Ubah Data Produk' }}</h3>
          <button
            class="btn-close"
            @click="closeModal"
          >
            &times;
          </button>
        </div>

        <form @submit.prevent="submitForm">
          <div class="modal-body">
            <div
              v-if="validationErrors.length"
              class="alert alert-danger"
              style="margin-bottom: 14px;"
            >
              <strong>Validasi Gagal (HTTP 422):</strong>
              <ul style="margin-left: 20px; margin-top: 6px;">
                <li
                  v-for="(err, idx) in validationErrors"
                  :key="idx"
                >
                  {{ err }}
                </li>
              </ul>
            </div>

            <div class="form-group">
              <label>Nama Produk <span class="required">*</span></label>
              <input
                v-model="formData.nama"
                type="text"
                placeholder="Contoh: Asus ROG Strix G16"
                required
                class="form-control"
              >
            </div>

            <div class="form-row">
              <div class="form-group col-half">
                <label>Kategori <span class="required">*</span></label>
                <input
                  v-model="formData.kategori"
                  type="text"
                  placeholder="Contoh: Laptop"
                  required
                  class="form-control"
                >
              </div>

              <div class="form-group col-half">
                <label>Stok (Unit) <span class="required">*</span></label>
                <input
                  v-model.number="formData.stok"
                  type="number"
                  min="0"
                  placeholder="10"
                  required
                  class="form-control"
                >
              </div>
            </div>

            <div class="form-group">
              <label>Harga Satuan (Rp) <span class="required">*</span></label>
              <input
                v-model.number="formData.harga"
                type="number"
                min="0"
                step="1000"
                placeholder="15000000"
                required
                class="form-control"
              >
            </div>

            <div class="form-group">
              <label>Deskripsi Tambahan</label>
              <textarea
                v-model="formData.deskripsi"
                rows="3"
                placeholder="Keterangan spesifikasi produk..."
                class="form-control"
              />
            </div>
          </div>

          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              @click="closeModal"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="submitting"
              class="btn btn-primary"
            >
              <span v-if="submitting">Menyimpan...</span>
              <span v-else>{{ modalMode === 'create' ? 'Simpan Produk' : 'Perbarui Produk' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import apiClient from '../api/axios'

const produks = ref([])
const loading = ref(true)
const submitting = ref(false)
const searchQuery = ref('')
const successMessage = ref('')
const errorMessage = ref('')
const validationErrors = ref([])

const showModal = ref(false)
const modalMode = ref('create') // 'create' | 'edit'
const selectedId = ref(null)

const formData = reactive({
  nama: '',
  kategori: '',
  harga: 0,
  stok: 0,
  deskripsi: '',
})

// Format mata uang rupiah
const formatCurrency = (val) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
  }).format(val)
}

// Filtered produk berdasarkan pencarian
const filteredProduks = computed(() => {
  if (!searchQuery.value.trim()) return produks.value
  const q = searchQuery.value.toLowerCase()
  return produks.value.filter(
    (p) =>
      p.nama.toLowerCase().includes(q) ||
      p.kategori.toLowerCase().includes(q)
  )
})

// Mengambil seluruh produk dari RESTful API via Axios (Soal 2b)
const fetchProduks = async () => {
  loading.value = true
  errorMessage.value = ''
  try {
    const res = await apiClient.get('/api/produk')
    produks.value = res.data.data || []
  } catch (err) {
    errorMessage.value = 'Gagal memuat data produk: ' + (err.response?.data?.message || err.message)
  } finally {
    loading.value = false
  }
}

// Buka Modal
const openModal = (mode, item = null) => {
  modalMode.value = mode
  validationErrors.value = []
  if (mode === 'edit' && item) {
    selectedId.value = item.id
    formData.nama = item.nama
    formData.kategori = item.kategori
    formData.harga = Number(item.harga)
    formData.stok = Number(item.stok)
    formData.deskripsi = item.deskripsi || ''
  } else {
    selectedId.value = null
    formData.nama = ''
    formData.kategori = ''
    formData.harga = 0
    formData.stok = 0
    formData.deskripsi = ''
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  validationErrors.value = []
}

// Submit Form (Tambah / Ubah via Axios)
const submitForm = async () => {
  submitting.value = true
  validationErrors.value = []
  errorMessage.value = ''
  successMessage.value = ''

  try {
    if (modalMode.value === 'create') {
      // POST /api/produk -> status 201 Created
      const res = await apiClient.post('/api/produk', formData)
      successMessage.value = 'Produk berhasil ditambahkan (HTTP ' + res.status + ' Created)!'
    } else {
      // PUT /api/produk/:id
      const res = await apiClient.put(`/api/produk/${selectedId.value}`, formData)
      successMessage.value = 'Produk berhasil diperbarui (HTTP ' + res.status + ')!'
    }

    closeModal()
    await fetchProduks()
  } catch (err) {
    if (err.response && err.response.status === 422) {
      const errorsObj = err.response.data.errors || {}
      validationErrors.value = Object.values(errorsObj).flat()
    } else {
      errorMessage.value = 'Terjadi kesalahan: ' + (err.response?.data?.message || err.message)
    }
  } finally {
    submitting.value = false
  }
}

// Hapus Produk via Axios (DELETE /api/produk/:id)
const confirmDelete = async (item) => {
  if (!confirm(`Yakin ingin menghapus produk "${item.nama}"?`)) return

  try {
    await apiClient.delete(`/api/produk/${item.id}`)
    successMessage.value = `Produk "${item.nama}" berhasil dihapus!`
    await fetchProduks()
  } catch (err) {
    errorMessage.value = 'Gagal menghapus produk: ' + (err.response?.data?.message || err.message)
  }
}

onMounted(() => {
  fetchProduks()
})
</script>

<style scoped>
.container {
  max-width: 1100px;
  margin: 30px auto;
  padding: 0 20px;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.page-title {
  font-size: 1.6rem;
  font-weight: 700;
  color: #111827;
}

.page-subtitle {
  color: #6b7280;
  font-size: 0.92rem;
  margin-top: 4px;
}

.toolbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  gap: 16px;
}

.search-box {
  flex: 1;
  max-width: 400px;
}

.stats-badge {
  background: #f3f4f6;
  padding: 8px 16px;
  border-radius: 6px;
  font-size: 0.88rem;
  color: #4b5563;
  border: 1px solid #e5e7eb;
}

.table-card {
  background: #ffffff;
  border-radius: 10px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  border: 1px solid #e5e7eb;
  overflow: hidden;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
  font-size: 0.92rem;
}

.data-table th {
  background: #f9fafb;
  color: #4b5563;
  padding: 12px 16px;
  font-weight: 600;
  border-bottom: 1px solid #e5e7eb;
}

.data-table td {
  padding: 14px 16px;
  border-bottom: 1px solid #f3f4f6;
  color: #1f2937;
}

.data-table tr:hover {
  background-color: #f9fafb;
}

.font-bold {
  font-weight: 600;
}

.text-muted {
  color: #9ca3af;
  font-size: 0.85rem;
}

.text-success {
  color: #059669;
}

.text-desc {
  max-width: 250px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  color: #6b7280;
  font-size: 0.88rem;
}

.badge {
  display: inline-block;
  padding: 3px 8px;
  border-radius: 12px;
  font-size: 0.78rem;
  font-weight: 600;
}

.badge-category {
  background-color: #e0e7ff;
  color: #4338ca;
}

.badge-in-stock {
  background-color: #d1fae5;
  color: #065f46;
}

.badge-low-stock {
  background-color: #fee2e2;
  color: #991b1b;
}

.action-cell {
  text-align: center;
}

.btn-action {
  background: none;
  border: 1px solid #d1d5db;
  border-radius: 4px;
  padding: 4px 8px;
  font-size: 0.9rem;
  cursor: pointer;
  margin: 0 4px;
  transition: all 0.2s;
}

.btn-action:hover {
  background-color: #f3f4f6;
}

.btn-edit:hover {
  border-color: #2563eb;
}

.btn-delete:hover {
  border-color: #dc2626;
}

.btn {
  padding: 9px 18px;
  border-radius: 6px;
  font-weight: 600;
  font-size: 0.9rem;
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

.btn-secondary {
  background-color: #f3f4f6;
  color: #374151;
  border: 1px solid #d1d5db;
}

.btn-secondary:hover {
  background-color: #e5e7eb;
}

.form-control {
  width: 100%;
  padding: 9px 12px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 0.92rem;
  outline: none;
}

.form-control:focus {
  border-color: #2563eb;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
}

.alert {
  padding: 12px 16px;
  border-radius: 6px;
  font-size: 0.9rem;
  margin-bottom: 20px;
}

.alert-success {
  background-color: #dcfce7;
  color: #15803d;
  border: 1px solid #86efac;
}

.alert-danger {
  background-color: #fee2e2;
  color: #b91c1c;
  border: 1px solid #fca5a5;
}

.empty-state {
  background: #ffffff;
  border-radius: 10px;
  padding: 60px 20px;
  text-align: center;
  border: 1px dashed #d1d5db;
}

.spinner {
  width: 32px;
  height: 32px;
  border: 3px solid #e5e7eb;
  border-top-color: #2563eb;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin: 0 auto 12px;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Modal styles */
.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 20px;
}

.modal-card {
  width: 100%;
  max-width: 520px;
  background: #ffffff;
  border-radius: 10px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
  overflow: hidden;
}

.modal-header {
  padding: 16px 20px;
  background: #f9fafb;
  border-bottom: 1px solid #e5e7eb;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-header h3 {
  font-size: 1.15rem;
  color: #1f2937;
  margin: 0;
}

.btn-close {
  background: none;
  border: none;
  font-size: 1.4rem;
  color: #9ca3af;
  cursor: pointer;
}

.btn-close:hover {
  color: #111827;
}

.modal-body {
  padding: 20px;
}

.form-group {
  margin-bottom: 14px;
}

.form-group label {
  display: block;
  font-size: 0.85rem;
  font-weight: 600;
  color: #374151;
  margin-bottom: 5px;
}

.required {
  color: #dc2626;
}

.form-row {
  display: flex;
  gap: 14px;
}

.col-half {
  flex: 1;
}

.modal-footer {
  padding: 14px 20px;
  background: #f9fafb;
  border-top: 1px solid #e5e7eb;
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}
</style>
