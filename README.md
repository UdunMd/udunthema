# [![Alxzen Panel](https://cdn.pterodactyl.io/logos/new/pterodactyl_logo.png)](https://github.com/alxzy-group/alxzen)

![GitHub Release](https://img.shields.io/github/v/release/alxzy-group/alxzen?style=for-the-badge&color=6c5ce7&label=Panel)
![GitHub Release](https://img.shields.io/github/v/release/alxzy-group/wings?style=for-the-badge&color=00b894&label=Wings)
![PHP Version](https://img.shields.io/badge/PHP-8.2%20%7C%208.3-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Node Version](https://img.shields.io/badge/Node.js-22+-339933?style=for-the-badge&logo=node.js&logoColor=white)
![License](https://img.shields.io/github/license/alxzy-group/alxzen?style=for-the-badge)

# Alxzen Panel

**Alxzen** is a customized distribution of the Pterodactyl® game server management panel, built with a focus on automation, aesthetic dominance, and system integrity. Featuring the **Alxzen Dark Purple** interface and an integrated **Expiration Management** system.

![alxzen Preview](https://cdn.pterodactyl.io/site-assets/pterodactyl_v1_demo.gif)

---

## ✨ Key Features

| Feature | Description |
|---|---|
| **🎨 Dark Purple UI** | Completely overhauled admin & user interface with deep blacks and electric purples |
| **⏰ Expiration Manager** | Direct admin control over server life-cycles with automated daily checks |
| **🔒 Auto-Suspension** | Native integration with suspension engine for expired instances |
| **🛡️ Root Protection v3.0** | Enhanced middleware ensuring core settings remain exclusive to primary admin |
| **⚡ Route Cache Compatible** | All admin routes use proper middleware — `php artisan optimize` works flawlessly |
| **🔧 Hardcoded Branding** | Permanent brand integrity for alxzen across the entire system |

---

## 🚀 Quick Install (Recommended)

Run this one-liner as **root** on a **fresh** Ubuntu/Debian server:

```bash
bash <(curl -s https://raw.githubusercontent.com/alxzy-group/alxzen/main/setup.sh)
```

The interactive installer will guide you through:
- **[1] Install Panel Only** — Full panel with PHP, MariaDB, Redis, Nginx, SSL
- **[2] Install Wings Only** — Docker + Wings daemon for game server nodes
- **[3] Install Panel + Wings** — Everything on one machine
- **[4] Uninstall Panel**
- **[5] Uninstall Wings**
- **[0] Exit**

### Supported Operating Systems

| OS | Versions |
|---|---|
| Ubuntu | 20.04, 22.04, 24.04 |
| Debian | 11, 12, 13 |

> **⚠️ Important:** Run only on a **fresh OS installation** as root. Do **not** install original Pterodactyl first.

---

## 📋 Manual Installation (Panel)

If you prefer to install manually, follow these steps on Ubuntu 22.04/24.04 or Debian 12/13.

### Prerequisites

| Requirement | Version |
|---|---|
| PHP | 8.2 or 8.3 |
| Composer | v2 |
| Node.js | 22+ |
| MariaDB | 10.6+ |
| Redis | 6+ |
| Nginx | Latest |
| OS | Ubuntu 20.04+, Debian 11+ |

### Step 1: Install Dependencies

```bash
# Update system
apt update && apt upgrade -y

# Install base packages
apt install -y software-properties-common curl apt-transport-https ca-certificates \
    gnupg tar unzip git wget lsb-release

# Add PHP repository (Ubuntu)
LC_ALL=C.UTF-8 add-apt-repository -y ppa:ondrej/php

# Add PHP repository (Debian)
# curl -sSLo /usr/share/keyrings/php-sury.gpg https://packages.sury.org/php/apt.gpg
# echo "deb [signed-by=/usr/share/keyrings/php-sury.gpg] https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/sury-php.list

# Add Redis repository
curl -fsSL https://packages.redis.io/gpg | gpg --dearmor -o /usr/share/keyrings/redis-archive-keyring.gpg --yes
echo "deb [signed-by=/usr/share/keyrings/redis-archive-keyring.gpg] https://packages.redis.io/deb $(lsb_release -cs) main" \
    | tee /etc/apt/sources.list.d/redis.list

# Install PHP 8.3, MariaDB, Nginx, Redis
apt update
apt install -y php8.3 php8.3-{common,cli,gd,mysql,mbstring,bcmath,xml,fpm,curl,zip,intl,sqlite3} \
    mariadb-server nginx redis-server

# Install Composer
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install Node.js 22
curl -fsSL https://deb.nodesource.com/setup_22.x | bash -
apt install -y nodejs
corepack enable
```

### Step 2: Setup Database

```bash
mysql -u root -e "CREATE USER 'pterodactyl'@'127.0.0.1' IDENTIFIED BY 'YOUR_SECURE_PASSWORD';"
mysql -u root -e "CREATE DATABASE panel;"
mysql -u root -e "GRANT ALL PRIVILEGES ON panel.* TO 'pterodactyl'@'127.0.0.1' WITH GRANT OPTION;"
mysql -u root -e "FLUSH PRIVILEGES;"
```

> Replace `YOUR_SECURE_PASSWORD` with a strong, randomly generated password.

### Step 3: Download & Install Panel

```bash
mkdir -p /var/www/pterodactyl
cd /var/www/pterodactyl

# Download latest release
curl -Lo panel.tar.gz https://github.com/alxzy-group/alxzen/releases/latest/download/panel.tar.gz
tar -xzf panel.tar.gz
rm panel.tar.gz

# Configure environment
cp .env.example .env

# Edit .env with your settings:
# - APP_URL=https://panel.yourdomain.com
# - DB_PASSWORD=YOUR_SECURE_PASSWORD
# - APP_TIMEZONE=Your/Timezone

# Install Composer dependencies
composer install --no-dev --optimize-autoloader

# Generate app key
php artisan key:generate --force

# Run database migrations
php artisan migrate --seed --force
```

### Step 4: Set Permissions

```bash
chown -R www-data:www-data /var/www/pterodactyl/*
chown -R www-data:www-data /var/www/pterodactyl/.[!.]*
chmod -R 755 storage/* bootstrap/cache/
```

### Step 5: Configure Cron Job

```bash
# Add to root crontab
(crontab -l 2>/dev/null; echo "* * * * * php /var/www/pterodactyl/artisan schedule:run >> /dev/null 2>&1") | crontab -
```

### Step 6: Queue Worker Service

Create the systemd service:

```bash
cat > /etc/systemd/system/pteroq.service <<'EOF'
[Unit]
Description=Alxzen Panel Queue Worker
After=redis-server.service

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/pterodactyl/artisan queue:work --queue=high,standard,low --sleep=3 --tries=3
StartLimitInterval=180
StartLimitBurst=30
RestartSec=5s

[Install]
WantedBy=multi-user.target
EOF

systemctl enable --now pteroq.service
```

### Step 7: Configure Nginx

Create the Nginx virtual host:

#### Without SSL (HTTP only)

```bash
cat > /etc/nginx/sites-available/pterodactyl.conf <<'EOF'
server {
    listen 80;
    server_name panel.yourdomain.com;

    root /var/www/pterodactyl/public;
    index index.php;

    access_log /var/log/nginx/pterodactyl.app-access.log;
    error_log  /var/log/nginx/pterodactyl.app-error.log error;

    client_max_body_size 100m;
    client_body_timeout 120s;
    sendfile off;

    add_header X-Content-Type-Options nosniff;
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Robots-Tag none;
    add_header Content-Security-Policy "frame-ancestors 'self'";
    add_header X-Frame-Options DENY;
    add_header Referrer-Policy same-origin;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param PHP_VALUE "upload_max_filesize = 100M \n post_max_size=100M";
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param HTTP_PROXY "";
        fastcgi_intercept_errors off;
        fastcgi_buffer_size 16k;
        fastcgi_buffers 4 16k;
        fastcgi_connect_timeout 300;
        fastcgi_send_timeout 300;
        fastcgi_read_timeout 300;
    }

    location ~ /\.ht {
        deny all;
    }
}
EOF
```

#### With SSL (HTTPS — Recommended)

```bash
# Install Certbot
apt install -y certbot python3-certbot-nginx

# Get SSL certificate
certbot certonly --nginx -d panel.yourdomain.com

# Create Nginx config
cat > /etc/nginx/sites-available/pterodactyl.conf <<'EOF'
server {
    listen 80;
    server_name panel.yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name panel.yourdomain.com;

    root /var/www/pterodactyl/public;
    index index.php;

    access_log /var/log/nginx/pterodactyl.app-access.log;
    error_log  /var/log/nginx/pterodactyl.app-error.log error;

    client_max_body_size 100m;
    client_body_timeout 120s;
    sendfile off;

    ssl_certificate /etc/letsencrypt/live/panel.yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/panel.yourdomain.com/privkey.pem;
    ssl_session_cache shared:SSL:10m;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers "ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384:ECDHE-ECDSA-CHACHA20-POLY1305:ECDHE-RSA-CHACHA20-POLY1305:DHE-RSA-AES128-GCM-SHA256:DHE-RSA-AES256-GCM-SHA384";
    ssl_prefer_server_ciphers on;

    add_header X-Content-Type-Options nosniff;
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Robots-Tag none;
    add_header Content-Security-Policy "frame-ancestors 'self'";
    add_header X-Frame-Options DENY;
    add_header Referrer-Policy same-origin;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param PHP_VALUE "upload_max_filesize = 100M \n post_max_size=100M";
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param HTTP_PROXY "";
        fastcgi_intercept_errors off;
        fastcgi_buffer_size 16k;
        fastcgi_buffers 4 16k;
        fastcgi_connect_timeout 300;
        fastcgi_send_timeout 300;
        fastcgi_read_timeout 300;
    }

    location ~ /\.ht {
        deny all;
    }
}
EOF
```

Then enable the site:

```bash
ln -s /etc/nginx/sites-available/pterodactyl.conf /etc/nginx/sites-enabled/pterodactyl.conf
rm -f /etc/nginx/sites-enabled/default

# Test & restart
nginx -t
systemctl restart nginx
```

### Step 8: Create Admin User

```bash
cd /var/www/pterodactyl
php artisan p:user:make
```

### Step 9: Firewall (Optional)

```bash
ufw allow 22    # SSH
ufw allow 80    # HTTP
ufw allow 443   # HTTPS
ufw enable
```

---

## 🦅 Wings Installation

Wings is the daemon that runs on your game server nodes. Install it on each node machine.

### Quick Install

Use the auto-installer:

```bash
bash <(curl -s https://raw.githubusercontent.com/alxzy-group/alxzen/main/setup.sh)
# Select option [2] Install Wings Only
```

### Manual Install

```bash
# Install Docker
curl -fsSL https://get.docker.com | sh
systemctl enable --now docker

# Create config directory
mkdir -p /etc/pterodactyl

# Download Wings binary
curl -L -o /usr/local/bin/wings \
    https://github.com/alxzy-group/wings/releases/latest/download/wings_linux_amd64
chmod u+x /usr/local/bin/wings
```

### Configure Wings Service

```bash
cat > /etc/systemd/system/wings.service <<'EOF'
[Unit]
Description=Pterodactyl Wings Daemon
After=docker.service
Requires=docker.service
PartOf=docker.service

[Service]
User=root
WorkingDirectory=/etc/pterodactyl
LimitNOFILE=4096
PIDFile=/var/run/wings/daemon.pid
ExecStart=/usr/local/bin/wings
Restart=on-failure
StartLimitInterval=180
StartLimitBurst=30
RestartSec=5s

[Install]
WantedBy=multi-user.target
EOF

systemctl enable wings
```

### Configure Node from Panel

1. Go to **Admin Panel → Nodes → Create New**
2. Fill in FQDN, memory, disk, etc.
3. Go to the **Configuration** tab
4. Copy the auto-deploy command and run it on your node:

```bash
# Example auto-deploy command (from your panel):
cd /etc/pterodactyl && sudo wings configure --panel-url https://panel.yourdomain.com --token YOUR_TOKEN --node 1
```

5. Start Wings:

```bash
sudo systemctl start wings
```

6. Verify Wings is running:

```bash
sudo systemctl status wings
# or debug mode:
sudo wings --debug
```

### Wings Firewall

```bash
ufw allow 8080   # Wings HTTP
ufw allow 2022   # Wings SFTP
ufw allow 443    # Wings HTTPS
ufw allow 25565:25575/tcp   # Minecraft
ufw allow 25565:25575/udp   # Minecraft
```

---

## 🔄 Upgrading from Official Pterodactyl

If you already have Pterodactyl installed and want to switch to **Alxzen**:

```bash
cd /var/www/pterodactyl
php artisan down

# Step 1: Backup .env
cp .env ../.env.backup

# Step 2: Remove old files (storage & .env are preserved)
rm -rf app bootstrap config database public resources routes tests \
  .editorconfig .env.example .eslintignore .eslintrc.js \
  .gitattributes .gitignore .prettierignore .prettierrc artisan \
  babel.config.js composer.json composer.lock jest.config.js \
  package.json phpstan.neon postcss.config.js SECURITY.md \
  tailwind.config.js tsconfig.json webpack.config.js yarn.lock

# Step 3: Download Alxzen release
curl -L https://github.com/alxzy-group/alxzen/releases/latest/download/panel.tar.gz | tar -xzv

# Step 4: Restore .env
cp ../.env.backup .env

# Step 5: Install dependencies
composer install --no-dev --optimize-autoloader

# Step 6: Finalize
php artisan view:clear && php artisan config:clear
php artisan migrate --force
chown -R www-data:www-data /var/www/pterodactyl/*
chown -R www-data:www-data /var/www/pterodactyl/.*
php artisan up
php artisan queue:restart
php artisan optimize
```

---

## 🔧 Troubleshooting

### ❌ `php artisan optimize` / `php artisan route:cache` Error

**Error:** `Call to undefined method Closure::__set_state()`

**Cause:** Before v2.1, some route groups used inline Closure middleware. Laravel can't cache Closures.

**Status:** ✅ **Fixed in v2.1** — All Closure middleware replaced with `RequireAdminUserId` class.

### ❌ WebSocket Error: "There was an error validating the credentials..."

This is **not a panel bug** — it's a Wings configuration issue. Common causes:

1. **Wings not running:** `systemctl status wings` — if not running, configure from Panel → Nodes → Auto-Deploy
2. **FQDN unreachable:** Make sure your node FQDN resolves publicly. Open ports: `ufw allow 8080 && ufw allow 2022`
3. **SSL mismatch:** If panel uses HTTPS, Wings must also use SSL:

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

4. **Database not migrated:** `php artisan migrate --force`

### ❌ 500 Error / Blank Page

```bash
cd /var/www/pterodactyl

# Check Laravel log
tail -50 storage/logs/laravel-$(date +%Y-%m-%d).log

# Fix permissions
chown -R www-data:www-data *
chown -R www-data:www-data .[!.]*
chmod -R 755 storage/* bootstrap/cache/

# Clear caches
php artisan config:clear
php artisan view:clear
php artisan cache:clear
php artisan optimize
```

### ❌ Nginx 502 Bad Gateway

```bash
# Check PHP-FPM is running
systemctl status php8.3-fpm

# Restart if needed
systemctl restart php8.3-fpm
systemctl restart nginx
```

---

## 📝 Changelog

### v3.1 — Complete Installer Overhaul
- **feat:** Complete interactive installer with menu system (Panel/Wings/Both/Uninstall)
- **feat:** Full Nginx virtual host configuration (HTTP & HTTPS)
- **feat:** Let's Encrypt SSL integration via Certbot
- **feat:** Wings installation with Docker setup
- **feat:** UFW firewall configuration
- **feat:** Uninstall options for both Panel and Wings
- **docs:** Complete README rewrite with full installation instructions
- **docs:** Added manual Nginx configuration guide
- **docs:** Added complete Wings installation section
- **docs:** Added troubleshooting for 500 errors and 502 Bad Gateway

### v3.0 — Pterodactyl 1.12.3 Updates
- Applied Pterodactyl 1.12.3 updates

### v2.2 — Pterodactyl 1.12.3 Updates
- Applied Pterodactyl 1.12.3 updates

### v2.1 — Route Cache Fix & Stability
- **fix:** Replaced all Closure middleware with `RequireAdminUserId` class
- **fix:** `php artisan route:cache` and `php artisan optimize` now work
- **feat:** Registered `admin.superuser` middleware alias in `Kernel.php`

### v2.0
- Initial Alxzen theme release with Dark Purple UI
- Expiration Manager with auto-suspension system
- Root Protection v3.0

---

## 📄 License

Alxzen Panel is based on Pterodactyl® which is licensed under the [MIT License](LICENSE.md).

© 2024-2026 [alxzy-group](https://github.com/alxzy-group) — Built with 💜
