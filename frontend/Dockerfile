# syntax=docker/dockerfile:1

# ================================================================
# STAGE 1 — builder
# Install dependencies dan build static files Vue menggunakan Node.js
# ================================================================
FROM node:20.19-alpine AS builder

WORKDIR /app

# Salin manifest DAHULU agar layer npm install ter-cache
COPY package.json package-lock.json ./
RUN npm ci --prefer-offline

# Salin seluruh source code, lalu build untuk production
COPY . .
RUN npm run build

# ================================================================
# STAGE 2 — runtime
# Hanya berisi Nginx + hasil build (dist/).
# node_modules TIDAK masuk ke image final.
# ================================================================
FROM nginx:1.27-alpine AS runtime

# Salin hasil build dari stage builder ke folder html Nginx
COPY --from=builder /app/dist /usr/share/nginx/html

# Konfigurasi Nginx untuk Vue Router (SPA history mode)
RUN echo 'server { \
    listen 80; \
    listen [::]:80; \
    root /usr/share/nginx/html; \
    index index.html; \
    location / { \
        try_files $uri $uri/ /index.html; \
    } \
}' > /etc/nginx/conf.d/default.conf

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
    CMD wget -q --spider http://127.0.0.1:80 || exit 1

CMD ["nginx", "-g", "daemon off;"]
