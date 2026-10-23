<template>
  <div class="about-container">
    <div class="card">
      <h2>ℹ️ Tentang Aplikasi</h2>
      <p class="desc">
        Aplikasi ini dikembangkan untuk memenuhi Ujian Tengah Semester (UTS) mata kuliah
        <strong>Konstruksi dan Evolusi Perangkat Lunak (SVPL26150)</strong>.
      </p>

      <div class="meta-section">
        <h3>Biodata Mahasiswa</h3>
        <table class="meta-table">
          <tr>
            <td><strong>Nama</strong></td>
            <td>: Hisyam Alrayyan</td>
          </tr>
          <tr>
            <td><strong>NIM</strong></td>
            <td>: 24/541582/SV/24926</td>
          </tr>
          <tr>
            <td><strong>Program Studi</strong></td>
            <td>: D4 Teknologi Rekayasa Perangkat Lunak</td>
          </tr>
          <tr>
            <td><strong>Departemen</strong></td>
            <td>: Teknik Elektro dan Informatika, Sekolah Vokasi UGM</td>
          </tr>
        </table>
      </div>

      <div class="meta-section">
        <h3>Spesifikasi Teknologi</h3>
        <ul class="tech-list">
          <li><strong>Backend:</strong> Laravel RESTful API dengan Laravel Sanctum Token Authentication</li>
          <li><strong>Frontend:</strong> Vue 3 (Composition API) + Vue Router + Axios Interceptor</li>
          <li><strong>Database:</strong> PostgreSQL / MySQL / SQLite</li>
          <li><strong>Containerization:</strong> Docker multi-stage & Docker Compose</li>
          <li><strong>CI/CD:</strong> GitHub Actions (Lint, Test, Build, Push ke GHCR)</li>
        </ul>
      </div>

      <div class="meta-section">
        <h3>Variabel Lingkungan (Environment Config)</h3>
        <p class="code-box">
          <code>VITE_API_URL: {{ apiUrl }}</code>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
const apiUrl = import.meta.env.VITE_API_URL || '(Default: http://localhost:8000)'
</script>

<style scoped>
.about-container {
  max-width: 800px;
  margin: 40px auto;
  padding: 0 20px;
}

.card {
  background: #ffffff;
  border-radius: 12px;
  padding: 32px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  border: 1px solid #e2e8f0;
}

h2 {
  color: #1e293b;
  margin-bottom: 12px;
}

.desc {
  color: #64748b;
  line-height: 1.6;
  margin-bottom: 24px;
}

.meta-section {
  margin-top: 24px;
  padding-top: 18px;
  border-top: 1px solid #f1f5f9;
}

.meta-section h3 {
  font-size: 1.1rem;
  color: #334155;
  margin-bottom: 12px;
}

.meta-table td {
  padding: 6px 12px 6px 0;
  color: #334155;
  font-size: 0.95rem;
}

.tech-list {
  padding-left: 20px;
  color: #475569;
  line-height: 1.8;
  font-size: 0.92rem;
}

.code-box {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  padding: 12px 16px;
  font-family: monospace;
  color: #2563eb;
  font-size: 0.92rem;
}
</style>
