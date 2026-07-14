#!/bin/bash
# ==============================================================================
# Udun Panel & Wings — Automated Installer Script
# Inspired by pterodactyl-installer (Bird)
# Supported OS: Ubuntu 20.04/22.04/24.04, Debian 11/12/13
#
# Copyright © 2024-2026 alxzy-group
# https://github.com/UdunMd/udunthema
# ==============================================================================

set -e

# ─── Colors & Formatting ─────────────────────────────────────────────────────
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
MAGENTA='\033[0;35m'
BOLD='\033[1m'
DIM='\033[2m'
NC='\033[0m'

# ─── Global Variables ────────────────────────────────────────────────────────
PANEL_DIR="/var/www/pterodactyl"
WINGS_DIR="/etc/pterodactyl"
WINGS_BIN="/usr/local/bin/wings"
GITHUB_PANEL="https://github.com/UdunMd/udunthema"
GITHUB_WINGS="https://github.com/alxzy-group/wings"
GITHUB_PANEL_DL="${GITHUB_PANEL}/releases/latest/download/panel.tar.gz"
GITHUB_WINGS_DL="${GITHUB_WINGS}/releases/latest/download/wings_linux_amd64"
# Fallback to official Pterodactyl wings if Udun wings release is not available
OFFICIAL_WINGS_DL="https://github.com/pterodactyl/wings/releases/latest/download/wings_linux_amd64"

OS=""
OS_VERSION=""
FQDN=""
ASSUME_SSL=false
CONFIGURE_UFW=false
DB_PASS=""

# ─── Helper Functions ────────────────────────────────────────────────────────

print_header() {
    clear
    echo ""
    echo -e "${MAGENTA}${BOLD}"
    echo "    ╔══════════════════════════════════════════════════════╗"
    echo "    ║                                                      ║"
    echo "    ║        █████╗ ██╗     ██╗  ██╗███████╗███████╗███╗   ║"
    echo "    ║       ██╔══██╗██║     ╚██╗██╔╝╚══███╔╝██╔════╝████╗  ║"
    echo "    ║       ███████║██║      ╚███╔╝   ███╔╝ █████╗  ██╔██╗ ║"
    echo "    ║       ██╔══██║██║      ██╔██╗  ███╔╝  ██╔══╝  ██║╚██╗║"
    echo "    ║       ██║  ██║███████╗██╔╝ ██╗███████╗███████╗██║ ╚██║║"
    echo "    ║       ╚═╝  ╚═╝╚══════╝╚═╝  ╚═╝╚══════╝╚══════╝╚═╝  ╚╝║"
    echo "    ║                                                      ║"
    echo "    ║         Udun Panel & Wings — Auto Installer        ║"
    echo "    ║              © 2024-2026 alxzy-group                 ║"
    echo "    ║                                                      ║"
    echo "    ╚══════════════════════════════════════════════════════╝"
    echo -e "${NC}"
}

print_step() {
    echo ""
    echo -e "  ${CYAN}${BOLD}▶ STEP${NC} ${BOLD}$1${NC}"
    echo -e "  ${DIM}$(printf '%.0s─' {1..50})${NC}"
}

print_ok() {
    echo -e "  ${GREEN}✓${NC} $1"
}

print_warn() {
    echo -e "  ${YELLOW}⚠${NC} $1"
}

print_err() {
    echo -e "  ${RED}✗ ERROR:${NC} $1"
    exit 1
}

print_info() {
    echo -e "  ${BLUE}ℹ${NC} $1"
}

# ─── OS Detection ────────────────────────────────────────────────────────────

detect_os() {
    if [ -f /etc/os-release ]; then
        . /etc/os-release
        OS="$ID"
        OS_VERSION="$VERSION_ID"
    else
        print_err "Cannot detect OS. /etc/os-release not found."
    fi

    case "$OS" in
        ubuntu)
            case "$OS_VERSION" in
                20.04|22.04|24.04) ;;
                *) print_err "Unsupported Ubuntu version: $OS_VERSION. Supported: 20.04, 22.04, 24.04" ;;
            esac
            ;;
        debian)
            case "$OS_VERSION" in
                11|12|13) ;;
                *) print_err "Unsupported Debian version: $OS_VERSION. Supported: 11, 12, 13" ;;
            esac
            ;;
        *)
            print_err "Unsupported OS: $OS. Only Ubuntu and Debian are supported."
            ;;
    esac

    print_ok "Detected OS: ${BOLD}${OS} ${OS_VERSION}${NC}"
}

# ─── Root Check ──────────────────────────────────────────────────────────────

check_root() {
    if [ "$EUID" -ne 0 ]; then
        print_err "This script must be run as root. Use: sudo bash setup.sh"
    fi
}

# ─── Ask Yes/No ──────────────────────────────────────────────────────────────

ask_yn() {
    local prompt="$1"
    local default="$2"
    local result

    if [ "$default" = "y" ]; then
        prompt="$prompt [Y/n]: "
    else
        prompt="$prompt [y/N]: "
    fi

    while true; do
        echo -ne "  ${YELLOW}?${NC} $prompt"
        read -r result
        result="${result:-$default}"
        case "$result" in
            [Yy]*) return 0 ;;
            [Nn]*) return 1 ;;
            *) echo -e "  ${RED}Please answer y or n.${NC}" ;;
        esac
    done
}

# ─── Ask Input ───────────────────────────────────────────────────────────────

ask_input() {
    local prompt="$1"
    local default="$2"
    local result

    if [ -n "$default" ]; then
        echo -ne "  ${YELLOW}?${NC} $prompt [${default}]: " >&2
    else
        echo -ne "  ${YELLOW}?${NC} $prompt: " >&2
    fi
    read -r result
    echo "${result:-$default}"
}

# ==============================================================================
#  PANEL INSTALLATION
# ==============================================================================

install_panel_dependencies() {
    print_step "Installing System Dependencies"

    apt-get update -y -qq
    apt-get upgrade -y -qq
    apt-get install -y -qq curl apt-transport-https \
        ca-certificates gnupg tar unzip git wget lsb-release cron
    
    if [ "$OS" == "ubuntu" ]; then
        apt-get install -y -qq software-properties-common
    fi
    print_ok "Base packages installed."

    # ── PHP 8.3 Repository ──
    print_step "Adding PHP 8.3 Repository"

    if [ "$OS" == "ubuntu" ]; then
        LC_ALL=C.UTF-8 add-apt-repository -y ppa:ondrej/php 2>/dev/null
    elif [ "$OS" == "debian" ]; then
        curl -sSLo /usr/share/keyrings/php-sury.gpg https://packages.sury.org/php/apt.gpg 2>/dev/null
        echo "deb [signed-by=/usr/share/keyrings/php-sury.gpg] https://packages.sury.org/php/ $(lsb_release -sc) main" \
            > /etc/apt/sources.list.d/sury-php.list
    fi
    print_ok "PHP repository added."

    # ── Redis Repository ──
    print_step "Adding Redis Repository"

    curl -fsSL https://packages.redis.io/gpg | gpg --dearmor -o /usr/share/keyrings/redis-archive-keyring.gpg --yes 2>/dev/null
    echo "deb [signed-by=/usr/share/keyrings/redis-archive-keyring.gpg] https://packages.redis.io/deb $(lsb_release -cs) main" \
        | tee /etc/apt/sources.list.d/redis.list > /dev/null
    print_ok "Redis repository added."

    # ── Install PHP, MariaDB, Nginx, Redis ──
    print_step "Installing PHP 8.3, MariaDB, Nginx & Redis"

    apt-get update -y -qq
    # Updated to avoid brace expansion issues with `sh`
    apt-get install -y -qq php8.3 php8.3-common php8.3-cli php8.3-gd php8.3-mysql php8.3-mbstring php8.3-bcmath php8.3-xml php8.3-fpm php8.3-curl php8.3-zip php8.3-intl php8.3-sqlite3 mariadb-server nginx redis-server
    print_ok "PHP 8.3, MariaDB, Nginx, and Redis installed."

    # ── Composer ──
    print_step "Installing Composer"

    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer 2>/dev/null
    print_ok "Composer installed."

    # ── Node.js 22 ──
    print_step "Installing Node.js 22"

    if ! command -v node &> /dev/null || [[ "$(node -v | cut -d'.' -f1 | tr -d 'v')" -lt 22 ]]; then
        curl -fsSL https://deb.nodesource.com/setup_22.x | bash - 2>/dev/null
        apt-get install -y -qq nodejs
    fi
    corepack enable 2>/dev/null || true
    print_ok "Node.js $(node -v) installed."
}

configure_database() {
    print_step "Configuring MariaDB Database"

    # Generate secure random password (alphanumeric only to avoid .env parsing issues with #)
    DB_PASS=$(tr -dc 'A-Za-z0-9' </dev/urandom | head -c 32)

    # Start MariaDB if not running
    systemctl enable --now mariadb 2>/dev/null

    mysql -u root <<MYSQL_SCRIPT
DROP USER IF EXISTS 'pterodactyl'@'127.0.0.1';
DROP USER IF EXISTS 'pterodactyl'@'localhost';
CREATE USER 'pterodactyl'@'127.0.0.1' IDENTIFIED BY '${DB_PASS}';
CREATE USER 'pterodactyl'@'localhost' IDENTIFIED BY '${DB_PASS}';
CREATE DATABASE IF NOT EXISTS panel;
GRANT ALL PRIVILEGES ON panel.* TO 'pterodactyl'@'127.0.0.1' WITH GRANT OPTION;
GRANT ALL PRIVILEGES ON panel.* TO 'pterodactyl'@'localhost' WITH GRANT OPTION;
FLUSH PRIVILEGES;
MYSQL_SCRIPT

    print_ok "Database 'panel' created."
    print_ok "User 'pterodactyl' configured."
}

download_panel() {
    print_step "Downloading Udun Panel"

    mkdir -p "$PANEL_DIR"
    cd "$PANEL_DIR"

    curl -Lo panel.tar.gz "$GITHUB_PANEL_DL" 2>/dev/null
    tar -xzf panel.tar.gz
    rm -f panel.tar.gz
    
    mkdir -p storage/logs storage/framework/{sessions,views,cache} bootstrap/cache
    chmod -R 755 storage/* bootstrap/cache/ 2>/dev/null || true

    print_ok "Panel files extracted to ${PANEL_DIR}"
}

configure_environment() {
    print_step "Configuring Panel Environment (.env)"

    cd "$PANEL_DIR"

    cp .env.example .env

    # Ensure APP_KEY exists so artisan key:generate works
    if ! grep -q "^APP_KEY=" .env; then
        echo "APP_KEY=SomeRandomString3232323232323232" >> .env
    fi

    local app_url
    if [ "$ASSUME_SSL" = true ]; then
        app_url="https://${FQDN}"
    else
        app_url="http://${FQDN}"
    fi

    local timezone
    timezone=$(ask_input "Application timezone" "UTC")

    sed -i "s|^APP_URL=.*|APP_URL=${app_url}|" .env
    sed -i "s|^APP_TIMEZONE=.*|APP_TIMEZONE=${timezone}|" .env
    sed -i "s|^DB_PASSWORD=.*|DB_PASSWORD=${DB_PASS}|" .env
    sed -i "s|^CACHE_DRIVER=.*|CACHE_DRIVER=redis|" .env
    sed -i "s|^SESSION_DRIVER=.*|SESSION_DRIVER=redis|" .env
    sed -i "s|^QUEUE_CONNECTION=.*|QUEUE_CONNECTION=redis|" .env

    print_ok "Environment configured with URL: ${app_url}"
}

install_panel_composer() {
    print_step "Installing Composer Dependencies (no-dev)"

    cd "$PANEL_DIR"
    rm -f composer.lock
    COMPOSER_ALLOW_SUPERUSER=1 COMPOSER_NO_AUDIT=1 php -d memory_limit=-1 /usr/local/bin/composer install --no-dev --optimize-autoloader --no-interaction --no-security-blocking

    print_ok "Composer dependencies installed."
}

setup_panel_artisan() {
    print_step "Running Artisan Setup"

    cd "$PANEL_DIR"

    # Generate application key
    php artisan key:generate --force --no-interaction
    print_ok "Application key generated."

    # Run database migrations
    php artisan migrate --seed --force --no-interaction
    print_ok "Database migrated and seeded."
}

set_panel_permissions() {
    print_step "Setting File Permissions"

    chown -R www-data:www-data "$PANEL_DIR"/*
    chown -R www-data:www-data "$PANEL_DIR"/.[!.]*
    chmod -R 755 "$PANEL_DIR"/storage/* "$PANEL_DIR"/bootstrap/cache/

    print_ok "Permissions set (owner: www-data)."
}

configure_crontab() {
    print_step "Configuring Cron Job (Task Scheduler)"

    # Safely pull current crontab or ignore if empty, then append scheduler
    crontab -l 2>/dev/null | grep -v "pterodactyl" > /tmp/crontab_new || true
    echo "* * * * * php ${PANEL_DIR}/artisan schedule:run >> /dev/null 2>&1" >> /tmp/crontab_new
    crontab /tmp/crontab_new
    rm -f /tmp/crontab_new

    print_ok "Cron job installed (runs every minute)."
}

configure_queue_worker() {
    print_step "Configuring Queue Worker (pteroq.service)"

    cat > /etc/systemd/system/pteroq.service <<'SYSTEMD_UNIT'
# Udun Panel Queue Worker
[Unit]
Description=Udun Panel Queue Worker
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
SYSTEMD_UNIT

    systemctl daemon-reload
    systemctl enable --now pteroq.service

    print_ok "Queue worker service installed and started."
}

configure_nginx_panel() {
    print_step "Configuring Nginx Web Server"

    # Remove default nginx site
    rm -f /etc/nginx/sites-enabled/default

    if [ "$ASSUME_SSL" = true ]; then
        # ── HTTPS (SSL) Configuration ──
        cat > /etc/nginx/sites-available/pterodactyl.conf <<NGINX_SSL
server {
    listen 80;
    server_name ${FQDN};
    return 301 https://\$server_name\$request_uri;
}

server {
    listen 443 ssl http2;
    server_name ${FQDN};

    root /var/www/pterodactyl/public;
    index index.php;

    access_log /var/log/nginx/pterodactyl.app-access.log;
    error_log  /var/log/nginx/pterodactyl.app-error.log error;

    # allow larger file uploads and longer script runtimes
    client_max_body_size 100m;
    client_body_timeout 120s;

    sendfile off;

    ssl_certificate /etc/letsencrypt/live/${FQDN}/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/${FQDN}/privkey.pem;
    ssl_session_cache shared:SSL:10m;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers "ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384:ECDHE-ECDSA-CHACHA20-POLY1305:ECDHE-RSA-CHACHA20-POLY1305:DHE-RSA-AES128-GCM-SHA256:DHE-RSA-AES256-GCM-SHA384";
    ssl_prefer_server_ciphers on;

    # Security headers
    add_header X-Content-Type-Options nosniff;
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Robots-Tag none;
    add_header Content-Security-Policy "frame-ancestors 'self'";
    add_header X-Frame-Options DENY;
    add_header Referrer-Policy same-origin;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php\$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)\$;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param PHP_VALUE "upload_max_filesize = 100M \n post_max_size=100M";
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
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
NGINX_SSL
    else
        # ── HTTP Only Configuration ──
        cat > /etc/nginx/sites-available/pterodactyl.conf <<NGINX_HTTP
server {
    listen 80;
    server_name ${FQDN};

    root /var/www/pterodactyl/public;
    index index.php;

    access_log /var/log/nginx/pterodactyl.app-access.log;
    error_log  /var/log/nginx/pterodactyl.app-error.log error;

    # allow larger file uploads and longer script runtimes
    client_max_body_size 100m;
    client_body_timeout 120s;

    sendfile off;

    # Security headers
    add_header X-Content-Type-Options nosniff;
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Robots-Tag none;
    add_header Content-Security-Policy "frame-ancestors 'self'";
    add_header X-Frame-Options DENY;
    add_header Referrer-Policy same-origin;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php\$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)\$;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param PHP_VALUE "upload_max_filesize = 100M \n post_max_size=100M";
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
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
NGINX_HTTP
    fi

    ln -sf /etc/nginx/sites-available/pterodactyl.conf /etc/nginx/sites-enabled/pterodactyl.conf

    # Test nginx config
    if nginx -t 2>/dev/null; then
        systemctl restart nginx
        print_ok "Nginx configured and restarted."
    else
        print_warn "Nginx config test failed. Please check /etc/nginx/sites-available/pterodactyl.conf"
    fi
}

configure_ssl() {
    print_step "Setting Up Let's Encrypt SSL Certificate"

    apt-get install -y -qq certbot python3-certbot-nginx

    # Stop nginx temporarily for standalone mode
    systemctl stop nginx

    if certbot certonly --standalone --non-interactive --agree-tos \
        --register-unsafely-without-email -d "$FQDN" 2>/dev/null; then
        print_ok "SSL certificate obtained for ${FQDN}"

        # Setup auto-renewal (safely using multi-line cron block)
        systemctl enable --now certbot.timer 2>/dev/null || {
            crontab -l 2>/dev/null | grep -v "certbot renew" > /tmp/crontab_new || true
            echo "0 23 * * * certbot renew --quiet --deploy-hook 'systemctl reload nginx'" >> /tmp/crontab_new
            crontab /tmp/crontab_new
            rm -f /tmp/crontab_new
        }

        print_ok "Auto-renewal configured."
    else
        print_warn "Failed to obtain SSL certificate. Make sure ${FQDN} points to this server."
        print_warn "You can retry later with: certbot certonly --nginx -d ${FQDN}"
        ASSUME_SSL=false
    fi

    systemctl start nginx
}

create_admin_user() {
    print_step "Creating Admin User"

    echo ""
    print_info "Let's create your first administrator account."
    echo ""

    cd "$PANEL_DIR"
    php artisan p:user:make

    print_ok "Admin user created."
}

configure_ufw_panel() {
    print_step "Configuring UFW Firewall (Panel)"

    apt-get install -y -qq ufw

    ufw allow 22 comment 'SSH' 2>/dev/null
    ufw allow 80 comment 'HTTP' 2>/dev/null
    ufw allow 443 comment 'HTTPS' 2>/dev/null

    echo "y" | ufw enable 2>/dev/null
    ufw reload 2>/dev/null

    print_ok "UFW enabled. Allowed ports: 22 (SSH), 80 (HTTP), 443 (HTTPS)"
}

# ==============================================================================
#  WINGS INSTALLATION
# ==============================================================================

install_docker() {
    print_step "Installing Docker"

    if command -v docker &> /dev/null; then
        print_ok "Docker is already installed: $(docker --version)"
        return
    fi

    curl -fsSL https://get.docker.com | sh 2>/dev/null

    systemctl enable --now docker

    print_ok "Docker installed: $(docker --version)"
}

install_wings_binary() {
    print_step "Downloading Udun Wings"

    mkdir -p "$WINGS_DIR"

    # Try Udun wings first, fallback to official
    if curl -L --fail -o "$WINGS_BIN" "$GITHUB_WINGS_DL" 2>/dev/null; then
        print_ok "Downloaded Udun Wings fork."
    elif curl -L --fail -o "$WINGS_BIN" "$OFFICIAL_WINGS_DL" 2>/dev/null; then
        print_warn "Udun Wings release not found. Using official Pterodactyl Wings."
    else
        print_err "Failed to download Wings binary. Please check your internet connection."
    fi

    chmod u+x "$WINGS_BIN"

    print_ok "Wings binary installed at ${WINGS_BIN}"
}

configure_wings_service() {
    print_step "Configuring Wings Systemd Service"

    cat > /etc/systemd/system/wings.service <<'SYSTEMD_UNIT'
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
SYSTEMD_UNIT

    systemctl daemon-reload
    systemctl enable wings

    print_ok "Wings service installed (not started — configure node from panel first)."
}

configure_ufw_wings() {
    print_step "Configuring UFW Firewall (Wings)"

    apt-get install -y -qq ufw

    ufw allow 22 comment 'SSH' 2>/dev/null
    ufw allow 8080 comment 'Wings HTTP' 2>/dev/null
    ufw allow 2022 comment 'Wings SFTP' 2>/dev/null
    ufw allow 443 comment 'Wings HTTPS' 2>/dev/null

    # Allow game server port range
    ufw allow 25565:25575/tcp comment 'Minecraft' 2>/dev/null
    ufw allow 25565:25575/udp comment 'Minecraft' 2>/dev/null
    ufw allow 27015:27030/tcp comment 'Source Games' 2>/dev/null
    ufw allow 27015:27030/udp comment 'Source Games' 2>/dev/null

    echo "y" | ufw enable 2>/dev/null
    ufw reload 2>/dev/null

    print_ok "UFW enabled. Allowed: 22, 443, 8080, 2022, 25565-25575, 27015-27030"
}

# ==============================================================================
#  UNINSTALL FUNCTIONS
# ==============================================================================

uninstall_panel() {
    print_header
    echo -e "  ${RED}${BOLD}⚠  UNINSTALL PANEL${NC}"
    echo ""
    print_warn "This will remove:"
    echo -e "    - Panel files at ${PANEL_DIR}"
    echo -e "    - Database 'panel' and user 'pterodactyl'"
    echo -e "    - Nginx config for pterodactyl"
    echo -e "    - pteroq systemd service"
    echo -e "    - Cron job for scheduler"
    echo ""

    if ! ask_yn "Are you sure you want to uninstall the panel?" "n"; then
        echo ""
        print_info "Uninstall cancelled."
        return
    fi

    echo ""

    # Stop services
    systemctl stop pteroq 2>/dev/null || true
    systemctl disable pteroq 2>/dev/null || true
    rm -f /etc/systemd/system/pteroq.service

    # Remove nginx config
    rm -f /etc/nginx/sites-enabled/pterodactyl.conf
    rm -f /etc/nginx/sites-available/pterodactyl.conf
    systemctl restart nginx 2>/dev/null || true

    # Remove cron (Safely handles empty crontab)
    crontab -l 2>/dev/null | grep -v "pterodactyl" > /tmp/crontab_new || true
    crontab /tmp/crontab_new
    rm -f /tmp/crontab_new

    # Remove database
    if command -v mysql &> /dev/null; then
        mysql -u root -e "DROP DATABASE IF EXISTS panel;" 2>/dev/null || true
        mysql -u root -e "DROP USER IF EXISTS 'pterodactyl'@'127.0.0.1';" 2>/dev/null || true
        mysql -u root -e "FLUSH PRIVILEGES;" 2>/dev/null || true
        print_ok "Database removed."
    fi

    # Remove panel files
    rm -rf "$PANEL_DIR"
    print_ok "Panel files removed."

    systemctl daemon-reload

    echo ""
    print_ok "${GREEN}${BOLD}Panel uninstalled successfully.${NC}"
}

uninstall_wings() {
    print_header
    echo -e "  ${RED}${BOLD}⚠  UNINSTALL WINGS${NC}"
    echo ""
    print_warn "This will remove:"
    echo -e "    - Wings binary at ${WINGS_BIN}"
    echo -e "    - Wings config at ${WINGS_DIR}"
    echo -e "    - Wings systemd service"
    echo ""

    if ! ask_yn "Are you sure you want to uninstall Wings?" "n"; then
        echo ""
        print_info "Uninstall cancelled."
        return
    fi

    echo ""

    # Stop & disable wings
    systemctl stop wings 2>/dev/null || true
    systemctl disable wings 2>/dev/null || true
    rm -f /etc/systemd/system/wings.service

    # Remove binary
    rm -f "$WINGS_BIN"

    # Ask about config
    if ask_yn "Remove Wings configuration (/etc/pterodactyl)?" "n"; then
        rm -rf "$WINGS_DIR"
        print_ok "Wings configuration removed."
    else
        print_info "Wings configuration preserved at ${WINGS_DIR}"
    fi

    # Ask about Docker
    if ask_yn "Remove Docker? (WARNING: This removes ALL containers/images)" "n"; then
        apt-get remove -y docker-ce docker-ce-cli containerd.io 2>/dev/null || true
        apt-get autoremove -y 2>/dev/null || true
        print_ok "Docker removed."
    else
        print_info "Docker preserved."
    fi

    systemctl daemon-reload

    echo ""
    print_ok "${GREEN}${BOLD}Wings uninstalled successfully.${NC}"
}

configure_wings_ssl() {
    print_step "Setting Up Let's Encrypt SSL Certificate for Wings Node"

    local wings_fqdn
    wings_fqdn=$(ask_input "Node FQDN (e.g. node.alxzyy.my.id)")

    apt-get install -y -qq certbot

    # Stop nginx temporarily for standalone mode if it's running
    systemctl stop nginx 2>/dev/null || true

    if certbot certonly --standalone --non-interactive --agree-tos \
        --register-unsafely-without-email -d "$wings_fqdn" 2>/dev/null; then
        print_ok "SSL certificate obtained for ${wings_fqdn}"

        # Setup auto-renewal (safely using multi-line cron block)
        systemctl enable --now certbot.timer 2>/dev/null || {
            crontab -l 2>/dev/null | grep -v "certbot renew" > /tmp/crontab_new || true
            echo "0 23 * * * certbot renew --quiet" >> /tmp/crontab_new
            crontab /tmp/crontab_new
            rm -f /tmp/crontab_new
        }

        print_ok "Auto-renewal configured."
    else
        print_warn "Failed to obtain SSL certificate. Make sure ${wings_fqdn} points to this server's IP."
        print_warn "You may need to configure certificates manually if DNS hasn't propagated."
    fi

    systemctl start nginx 2>/dev/null || true
}

# ==============================================================================
#  INSTALLATION ORCHESTRATORS
# ==============================================================================

update_panel() {
    print_header
    echo -e "  ${YELLOW}${BOLD}🔄 UPDATE PANEL${NC}"
    echo ""
    print_info "Updating Udun Panel..."
    
    cd "$PANEL_DIR"
    php artisan down

    # Download & extract latest release
    curl -Lo /tmp/panel.tar.gz "$GITHUB_PANEL_DL" 2>/dev/null
    tar -xzf /tmp/panel.tar.gz
    rm -f /tmp/panel.tar.gz

    # Clear caches & set permissions
    php artisan view:clear && php artisan config:clear
    chown -R www-data:www-data "$PANEL_DIR"/*
    chown -R www-data:www-data "$PANEL_DIR"/.[!.]*

    # Bring panel back online
    php artisan up
    
    echo ""
    print_ok "${GREEN}${BOLD}Panel updated successfully.${NC}"
}


install_panel() {
    print_header
    echo -e "  ${CYAN}${BOLD}📦 PANEL INSTALLATION${NC}"
    echo ""

    detect_os

    echo ""
    FQDN=$(ask_input "Panel FQDN (e.g., panel.example.com)" "")
    if [ -z "$FQDN" ]; then
        print_err "FQDN is required. Example: panel.example.com"
    fi

    echo ""
    if ask_yn "Configure Let's Encrypt SSL for ${FQDN}?" "y"; then
        ASSUME_SSL=true
    else
        ASSUME_SSL=false
    fi

    if ask_yn "Configure UFW firewall?" "y"; then
        CONFIGURE_UFW=true
    fi

    echo ""
    print_info "Starting installation for ${BOLD}${FQDN}${NC}..."
    echo ""

    install_panel_dependencies
    configure_database
    download_panel
    configure_environment

    install_panel_composer
    setup_panel_artisan
    set_panel_permissions
    configure_crontab
    configure_queue_worker

    if [ "$ASSUME_SSL" = true ]; then
        configure_ssl
    fi

    configure_nginx_panel

    if [ "$CONFIGURE_UFW" = true ]; then
        configure_ufw_panel
    fi

    # ── Summary ──
    echo ""
    echo -e "  ${GREEN}${BOLD}╔══════════════════════════════════════════════════════╗${NC}"
    echo -e "  ${GREEN}${BOLD}║                                                      ║${NC}"
    echo -e "  ${GREEN}${BOLD}║     ✓  Udun Panel Installation Complete!           ║${NC}"
    echo -e "  ${GREEN}${BOLD}║                                                      ║${NC}"
    echo -e "  ${GREEN}${BOLD}╚══════════════════════════════════════════════════════╝${NC}"
    echo ""
    echo -e "  ${BOLD}Panel URL:${NC}      $([ "$ASSUME_SSL" = true ] && echo "https://${FQDN}" || echo "http://${FQDN}")"
    echo -e "  ${BOLD}Panel Path:${NC}     ${PANEL_DIR}"
    echo -e "  ${BOLD}DB Name:${NC}        panel"
    echo -e "  ${BOLD}DB User:${NC}        pterodactyl@127.0.0.1"
    echo -e "  ${BOLD}DB Password:${NC}    ${DB_PASS}"
    echo ""
    echo -e "  ${YELLOW}${BOLD}⚠  SAVE YOUR DATABASE PASSWORD! It will not be shown again.${NC}"
    echo ""

    if ask_yn "Create admin user now?" "y"; then
        create_admin_user
    else
        echo ""
        print_info "You can create an admin later with:"
        echo -e "    ${CYAN}cd ${PANEL_DIR} && php artisan p:user:make${NC}"
    fi

    echo ""
    print_info "Next steps:"
    echo -e "    1. Visit your panel at $([ "$ASSUME_SSL" = true ] && echo "https://${FQDN}" || echo "http://${FQDN}")"
    echo -e "    2. Add a node from Admin → Nodes"
    echo -e "    3. Install Wings on your game server node"
    echo ""
}

install_wings() {
    print_header
    echo -e "  ${CYAN}${BOLD}🦅 WINGS INSTALLATION${NC}"
    echo ""

    detect_os

    local configure_ufw_wings_flag=false

    echo ""
    if ask_yn "Configure UFW firewall for Wings?" "y"; then
        configure_ufw_wings_flag=true
    fi

    local configure_ssl_wings_flag=false
    echo ""
    if ask_yn "Configure Let's Encrypt SSL for this Node?" "y"; then
        configure_ssl_wings_flag=true
    fi

    echo ""
    print_info "Starting Wings installation..."
    echo ""

    install_docker
    install_wings_binary
    configure_wings_service

    if [ "$configure_ufw_wings_flag" = true ]; then
        configure_ufw_wings
    fi

    if [ "$configure_ssl_wings_flag" = true ]; then
        configure_wings_ssl
    fi

    # ── Summary ──
    echo ""
    echo -e "  ${GREEN}${BOLD}╔══════════════════════════════════════════════════════╗${NC}"
    echo -e "  ${GREEN}${BOLD}║                                                      ║${NC}"
    echo -e "  ${GREEN}${BOLD}║     ✓  Udun Wings Installation Complete!           ║${NC}"
    echo -e "  ${GREEN}${BOLD}║                                                      ║${NC}"
    echo -e "  ${GREEN}${BOLD}╚══════════════════════════════════════════════════════╝${NC}"
    echo ""
    echo -e "  ${BOLD}Wings Binary:${NC}   ${WINGS_BIN}"
    echo -e "  ${BOLD}Config Dir:${NC}     ${WINGS_DIR}"
    echo ""
    print_info "Next steps:"
    echo -e "    1. Go to your Panel → Admin → Nodes → Create Node"
    echo -e "    2. Copy the auto-deploy command from the Configuration tab"
    echo -e "    3. Run the command on this server to generate config.yml"
    echo -e "    4. Start Wings:"
    echo -e "       ${CYAN}sudo systemctl start wings${NC}"
    echo ""
    print_info "To test Wings manually:"
    echo -e "    ${CYAN}sudo wings --debug${NC}"
    echo ""
}

install_both() {
    install_panel
    echo ""
    echo -e "  ${CYAN}${BOLD}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "  ${CYAN}${BOLD}  Panel done! Now installing Wings on the same machine...${NC}"
    echo -e "  ${CYAN}${BOLD}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo ""

    # Skip OS detection again, go straight to Wings
    local configure_ufw_wings_flag=false

    if ask_yn "Configure UFW firewall for Wings?" "y"; then
        configure_ufw_wings_flag=true
    fi

    install_docker
    install_wings_binary
    configure_wings_service

    if [ "$configure_ufw_wings_flag" = true ]; then
        configure_ufw_wings
    fi

    echo ""
    echo -e "  ${GREEN}${BOLD}╔══════════════════════════════════════════════════════╗${NC}"
    echo -e "  ${GREEN}${BOLD}║                                                      ║${NC}"
    echo -e "  ${GREEN}${BOLD}║   ✓  Udun Panel + Wings — Fully Installed!        ║${NC}"
    echo -e "  ${GREEN}${BOLD}║                                                      ║${NC}"
    echo -e "  ${GREEN}${BOLD}╚══════════════════════════════════════════════════════╝${NC}"
    echo ""
    echo -e "  ${BOLD}Panel URL:${NC}      $([ "$ASSUME_SSL" = true ] && echo "https://${FQDN}" || echo "http://${FQDN}")"
    echo -e "  ${BOLD}Panel Path:${NC}     ${PANEL_DIR}"
    echo -e "  ${BOLD}Wings Binary:${NC}   ${WINGS_BIN}"
    echo -e "  ${BOLD}Wings Config:${NC}   ${WINGS_DIR}"
    echo -e "  ${BOLD}DB Password:${NC}    ${DB_PASS}"
    echo ""
    echo -e "  ${YELLOW}${BOLD}⚠  SAVE YOUR DATABASE PASSWORD ABOVE!${NC}"
    echo ""
    print_info "Next steps:"
    echo -e "    1. Visit your panel and log in"
    echo -e "    2. Create a Node (Admin → Nodes) using FQDN: ${FQDN}"
    echo -e "    3. Use auto-deploy to configure Wings"
    echo -e "    4. Start Wings: ${CYAN}sudo systemctl start wings${NC}"
    echo ""
}

# ==============================================================================
#  MAIN MENU
# ==============================================================================

main_menu() {
    while true; do
        print_header

        echo -e "  ${BOLD}What would you like to do?${NC}"
        echo ""
        echo -e "    ${GREEN}[1]${NC} Install Panel Only"
        echo -e "    ${GREEN}[2]${NC} Install Wings Only"
        echo -e "    ${GREEN}[3]${NC} Install Panel + Wings (Same Machine)"
        echo ""
        echo -e "    ${YELLOW}[4]${NC} Update Panel"
        echo ""
        echo -e "    ${RED}[5]${NC} Uninstall Panel"
        echo -e "    ${RED}[6]${NC} Uninstall Wings"
        echo ""
        echo -e "    ${DIM}[0]${NC} Exit"
        echo ""
        echo -ne "  ${YELLOW}?${NC} ${BOLD}Enter your choice [0-6]:${NC} "
        read -r choice

        case "$choice" in
            1) install_panel; break ;;
            2) install_wings; break ;;
            3) install_both; break ;;
            4) update_panel; break ;;
            5) uninstall_panel; break ;;
            6) uninstall_wings; break ;;
            0)
                echo ""
                print_info "Goodbye! Visit ${CYAN}${GITHUB_PANEL}${NC} for docs & support."
                echo ""
                exit 0
                ;;
            *)
                print_warn "Invalid choice. Please select 0-5."
                sleep 1
                ;;
        esac
    done
}

# ==============================================================================
#  ENTRY POINT
# ==============================================================================

check_root
main_menu
