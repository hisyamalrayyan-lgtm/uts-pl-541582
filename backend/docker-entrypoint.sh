#!/bin/sh
set -e

# Pastikan file database/cache siap
mkdir -p storage/framework/{cache,sessions,views} bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Tunggu database siap jika menggunakan MySQL/MariaDB
if [ "$DB_CONNECTION" = "mysql" ]; then
    echo "Menunggu basis data MySQL ($DB_HOST:$DB_PORT) siap..."
    until nc -z -v -w30 "$DB_HOST" "$DB_PORT" > /dev/null 2>&1; do
        echo "Database belum siap, menunggu 2 detik..."
        sleep 2
    done
    echo "Basis data siap!"
fi

# Jalankan migrasi dan seed jika belum
php artisan migrate --force
php artisan db:seed --force

# Cache konfigurasi untuk performa produksi
php artisan config:cache || true
php artisan route:cache || true

echo "=== FrankenPHP Laravel Container Siap Melayani Request di Port 8000 ==="

exec "$@"
