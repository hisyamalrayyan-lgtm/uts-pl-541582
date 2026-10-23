# UTS Konstruksi dan Evolusi Perangkat Lunak (SVPL26150)

Repositori ini berisi kode sumber proyek Ujian Tengah Semester mata kuliah **Konstruksi dan Evolusi Perangkat Lunak (SVPL26150)**, Program Studi D4 Teknologi Rekayasa Perangkat Lunak, Departemen Teknik Elektro dan Informatika, Sekolah Vokasi, Universitas Gadjah Mada.

- **Nama Mahasiswa:** Hisyam Al Rayyan
- **NIM:** 24/541582/SV/24926
- **Kelas:** A2
- **Dosen Pengampu:** Dr. Muhammad Fakhrurrifqi, S.Kom., M.Cs. & Galih Malela Damaraji, S.Pd., M.Eng.
- **Tema Aplikasi:** Katalog Produk dan Inventaris (*Produk RESTful API*)

---

## Arsitektur & Teknologi

- **Backend:** Laravel 11, PHP 8.4, Laravel Sanctum (Token Auth), SQLite / MariaDB 11.4.
- **Frontend:** Vue 3 (Composition API), Vite, Axios (dengan Request Interceptor Bearer Token).
- **Web Server:** FrankenPHP 1.4 (Caddy Server dengan worker mode PHP).
- **Kontainerisasi:** Docker multi-stage build & Docker Compose.
- **CI/CD Pipeline:** GitHub Actions untuk automated testing (PHPUnit & Vitest) serta automated deployment Docker image ke GitHub Container Registry (`ghcr.io`).

---

## Struktur Repositori

```text
├── .github/
│   └── workflows/
│       └── ci.yml               # Pipeline CI (test backend & frontend) + CD (GHCR deploy)
├── backend/                     # Source code backend Laravel 11
│   ├── app/
│   │   ├── Http/Controllers/   # AuthController & ProdukController
│   │   └── Models/             # Model User & Produk
│   ├── routes/api.php          # Rute autentikasi & CRUD resource produk
│   ├── tests/Feature/          # Feature tests Laravel (21 tests)
│   ├── Dockerfile              # Multi-stage image backend CLI
│   ├── Dockerfile.frankenphp   # Image backend berbasis FrankenPHP Caddy
│   └── docker-entrypoint.sh    # Script inisialisasi kontainer
├── frontend/                    # Source code frontend Vue 3
│   ├── src/
│   │   ├── api/axios.js        # Konfigurasi Axios & Bearer token interceptor
│   │   ├── views/              # View Login, Katalog Produk, dan Tentang
│   │   └── __tests__/          # Unit tests Vitest (10 tests)
│   ├── Dockerfile              # Multi-stage build Vue (Node.js -> Nginx)
│   └── vite.config.js          # Konfigurasi Vite dev & build
├── docker-compose.yml           # Orkestrasi service MariaDB, Backend, dan Frontend
├── .env.example                 # Contoh template environment
└── postman_collection.json      # Koleksi endpoint Postman untuk pengujian API
```

---

## Cara Menjalankan Aplikasi

### 1. Menggunakan Docker Compose (Rekomendasi)

Pastikan Docker Desktop sudah aktif, lalu jalankan:

```bash
# 1. Salin file environment
cp .env.example .env

# 2. Jalankan seluruh kontainer di latar belakang
docker compose up -d

# 3. Periksa status layanan
docker compose ps
```

Aplikasi dapat diakses melalui browser:
- **Frontend Vue (Nginx):** `http://localhost:3000`
- **Backend API (FrankenPHP):** `http://localhost:8000`

Untuk menghentikan kontainer tanpa menghapus data persisten di volume:
```bash
docker compose down
```

---

### 2. Menjalankan Secara Manual (Development Lokal)

#### Backend (Laravel)
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve --port=8000
```

#### Frontend (Vue 3)
```bash
cd frontend
npm install
npm run dev
```
Akses frontend di `http://localhost:5173`.

---

## Pengujian Otomatis (Automated Testing)

- **Backend (PHPUnit):**
  ```bash
  cd backend
  php artisan test
  ```
  *(21 Feature Tests mencakup otentikasi register/login/logout dan seluruh skenario CRUD produk status 201/422/401/200)*.

- **Frontend (Vitest):**
  ```bash
  cd frontend
  npm test
  ```
  *(10 Unit Tests mencakup utilitas pemformatan data Rupiah dan label stok)*.

---

## Koleksi Postman

Berkas koleksi endpoint API telah disediakan pada file `postman_collection.json`. Berkas ini dapat langsung diimpor ke aplikasi Postman untuk menguji:
1. `POST /api/register` & `POST /api/login` (Autentikasi Sanctum)
2. `GET /api/me` & `POST /api/logout`
3. `GET /api/produk` (Katalog produk dengan token)
4. `POST /api/produk` (Tambah produk baru, validasi 201 Created & 422 Unprocessable)
5. `GET /api/produk/{id}` (Detail produk)
6. `PUT /api/produk/{id}` (Update data produk)
7. `DELETE /api/produk/{id}` (Hapus produk)
8. Pengujian akses tanpa token (Verifikasi 401 Unauthorized)
