# [![alxzen Logo](https://cdn.pterodactyl.io/logos/new/pterodactyl_logo.png)](https://github.com/alxzy-group/alxzen)

![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/pterodactyl/panel/ci.yaml?label=Tests&style=for-the-badge)
![Theme Version](https://img.shields.io/badge/Theme-alxzen_v2.1-6c5ce7?style=for-the-badge)
![Protect Version](https://img.shields.io/badge/Protect-v3.0-success?style=for-the-badge)
![Discord](https://img.shields.io/discord/122900397965705216?label=Discord&logo=Discord&logoColor=white&style=for-the-badge)
![Route Cache](https://img.shields.io/badge/artisan_route%3Acache-✅_Compatible-00b894?style=for-the-badge)

# alxzen Panel

**alxzen** is a highly customized distribution of the Pterodactyl® game server management panel. Built with a focus on automation, aesthetic dominance, and system integrity. This version features the **alxzen Dark Purple** interface and an integrated **Expiration Management** system to streamline hosting operations.

Stop settling for generic. Make your platform stand out with a first-class citizen UI and automated billing-ready features.

![alxzen Preview](https://cdn.pterodactyl.io/site-assets/pterodactyl_v1_demo.gif)

## Key Enhancements

* **Alxzen Dark Purple UI**: A completely overhauled administrative and user interface using deep blacks and electric purples.
* **Expiration Manager**: Direct administrative control over server life-cycles with automated daily checks.
* **Auto-Suspension System**: Native integration with the Pterodactyl suspension engine for expired instances.
* **Hardcoded Branding**: Permanent brand integrity for **alxzen** and **alxzy/alan** across the system.
* **Root Protection v3.0**: Enhanced middleware restrictions ensuring core settings remain exclusive to the primary administrator.
* **Route Cache Compatible**: All admin routes use proper middleware classes — `php artisan optimize` and `php artisan route:cache` work without errors.

---

## Installation & Update (Standalone Fork)

To install **alxzen**, you must first prepare your environment. The following instructions are designed for **Ubuntu 22.04 / 24.04**. Do **not** install the original Pterodactyl panel first.

### 1. Prerequisites Setup (PHP 8.3, MariaDB, Redis, Nginx)

```bash
# Add PHP 8.3 repository
apt update && apt -y install software-properties-common curl apt-transport-https ca-certificates gnupg
LC_ALL=C.UTF-8 add-apt-repository -y ppa:ondrej/php
curl -fsSL https://packages.redis.io/gpg | sudo gpg --dearmor -o /usr/share/keyrings/redis-archive-keyring.gpg
echo "deb [signed-by=/usr/share/keyrings/redis-archive-keyring.gpg] https://packages.redis.io/deb $(lsb_release -cs) main" | sudo tee /etc/apt/sources.list.d/redis.list

# Install Dependencies
apt update
apt -y install php8.3 php8.3-{common,cli,gd,mysql,mbstring,bcmath,xml,fpm,curl,zip} mariadb-server nginx tar unzip git redis-server

# Install Composer
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer

# Set up MariaDB
mysql -u root -e "CREATE USER 'pterodactyl'@'127.0.0.1' IDENTIFIED BY 'YOUR_PASSWORD_HERE';"
mysql -u root -e "CREATE DATABASE panel;"
mysql -u root -e "GRANT ALL PRIVILEGES ON panel.* TO 'pterodactyl'@'127.0.0.1' WITH GRANT OPTION;"
mysql -u root -e "FLUSH PRIVILEGES;"
```

### 2. Panel Deployment

```bash
# Prepare Directory
mkdir -p /var/www/pterodactyl
cd /var/www/pterodactyl

# Clone Repository
git clone https://github.com/alxzy-group/alxzen.git .
cp .env.example .env

# Install Node.js & Yarn (For compiling assets if necessary)
curl -fsSL https://deb.nodesource.com/setup_22.x | bash -
apt -y install nodejs
npm install -g yarn

# Optimization & Dependencies
composer install --no-dev --optimize-autoloader
yarn install
yarn build:production

# Environment Configuration -- Edit .env dulu sebelum lanjut!
cp .env.example .env
nano .env  # Isi: APP_URL, DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD
```

### 3. Finalization - Cukup 1 Perintah

Jalankan setup script yang otomatis mengurus: validasi APP_URL, clear cache, migrasi DB, set permissions, dan rebuild cache.

```bash
# Setup otomatis -- validasi config, migrasi, cache, permissions
bash setup.sh

# Buat akun admin pertama
php artisan p:user:make
```
*(Note: Don't forget to configure your Nginx virtual host and crontab as per the standard Pterodactyl documentation).*

---

## Upgrading / Migrating from Official Pterodactyl

If you already have a working Pterodactyl panel installed and want to switch to **alxzen** without losing your data, database, or `.env` file, use the following sequence:

```bash
cd /var/www/pterodactyl
php artisan down

# ── STEP 1: Backup .env ──────────────────────────────────────────────────────
cp .env ../.env.backup
# Optional: backup seluruh folder
# cp -r /var/www/pterodactyl /var/www/pterodactyl_backup

# ── STEP 2: Hapus file lama (storage dan .env TIDAK ikut terhapus) ───────────
rm -rf app bootstrap config database public resources routes tests \
  .editorconfig .env.example .eslintignore .eslintrc.js \
  .gitattributes .gitignore .prettierignore .prettierrc artisan \
  babel.config.js composer.json composer.lock jest.config.js \
  package.json phpstan.neon postcss.config.js SECURITY.md \
  tailwind.config.js tsconfig.json webpack.config.js yarn.lock

# ── STEP 3: Download & extract release alxzen ────────────────────────────────
curl -L https://github.com/alxzy-group/alxzen/releases/latest/download/panel.tar.gz | tar -xzv --strip-components=1

# ── STEP 4: Restore .env dari backup ─────────────────────────────────────────
# WAJIB! Release baru membawa .env.example — restore konfigurasi lamamu.
cp ../.env.backup .env

# ── STEP 5: Install Dependencies ─────────────────────────────────────────────
composer install --no-dev --optimize-autoloader
yarn install
yarn build:production

# ── STEP 6: Finalize & Cache ─────────────────────────────────────────────────
php artisan view:clear && php artisan config:clear
php artisan migrate --force
chown -R www-data:www-data /var/www/pterodactyl/*
chown -R www-data:www-data /var/www/pterodactyl/.*
php artisan up
php artisan queue:restart
php artisan optimize
```

---

## Installing Wings (alxzen Fork)

*(Segera hadir: Instruksi khusus untuk instalasi Wings fork dari alxzen)*
Untuk saat ini, jika Anda menggunakan wings fork alxzen, gunakan perintah instalasi:

```bash
mkdir -p /etc/pterodactyl
curl -L -o /usr/local/bin/wings https://github.com/alxzy-group/wings/releases/latest/download/wings_linux_amd64
chmod u+x /usr/local/bin/wings
```

---

## Troubleshooting

### ❌ `php artisan optimize` / `php artisan route:cache` Error

**Error:** `Call to undefined method Closure::__set_state()`

**Cause:** Sebelum v2.1, beberapa route group di `routes/admin.php` menggunakan inline Closure sebagai middleware. Laravel tidak bisa meng-cache Closure/anonymous function ke file PHP.

**Status:** ✅ **Fixed in v2.1** — Semua Closure middleware diganti dengan class `App\Http\Middleware\Admin\RequireAdminUserId`. Jalankan `php artisan optimize` dengan aman.

---

### ❌ WebSocket Error: "There was an error validating the credentials provided for the websocket"

Error ini **bukan dari kode tema** — frontend alxzen tidak memiliki hardcoded URL. Error ini terjadi karena Wings menolak JWT token yang dikirim panel. Biasanya terjadi pada instalasi fresh atau migrasi VPS.

**Penyebab & Fix:**

#### 1. Wings belum dikonfigurasi di node

```bash
# Cek status Wings
systemctl status wings

# Jika belum setup: Admin Panel → Nodes → [Node] → Auto-Deploy
# Salin perintah yang diberikan, jalankan di VPS node, lalu:
systemctl enable --now wings
```

#### 2. FQDN Node tidak bisa diakses dari internet

```bash
# Admin Panel → Nodes → [Node] → Settings
# Pastikan FQDN bisa di-resolve dari luar VPS
# Buka port Wings di firewall:
ufw allow 8080
ufw allow 2022
```

#### 3. SSL mismatch (Panel HTTPS, Wings HTTP)

Jika panel menggunakan `https://`, Wings **wajib** pakai SSL juga. Browser akan memblokir koneksi `wss://` ke Wings yang tidak ber-SSL.

```yaml
# /etc/pterodactyl/config.yml
ssl:
  enabled: true
  cert: /etc/letsencrypt/live/node.domain.com/fullchain.pem
  key: /etc/letsencrypt/live/node.domain.com/privkey.pem
```

```bash
systemctl restart wings
```

#### 4. Database belum di-migrate

```bash
php artisan migrate --force
```

---

## Changelog

### v2.1 — Route Cache Fix & Stability
- **fix:** Replaced all 4 inline Closure middleware in `routes/admin.php` with proper `RequireAdminUserId` middleware class
- **fix:** `php artisan route:cache` and `php artisan optimize` now work on any VPS without errors
- **feat:** Registered `admin.superuser` middleware alias in `Kernel.php`
- **docs:** Added Troubleshooting section for WebSocket and artisan errors
- **docs:** Added Changelog section
- **chore:** Removed unused `use Illuminate\Http\Request` import from `routes/admin.php`

### v2.0
- Initial alxzen theme release with Dark Purple UI
- Expiration Manager with auto-suspension system
- Root Protection v3.0
- Integrated Wings fork support
