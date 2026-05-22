# [![alxzen Logo](https://cdn.pterodactyl.io/logos/new/pterodactyl_logo.png)](https://github.com/alxzy-group/alxzen)

![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/pterodactyl/panel/ci.yaml?label=Tests&style=for-the-badge)
![Theme Version](https://img.shields.io/badge/Theme-alxzen_v2.0-6c5ce7?style=for-the-badge)
![Protect Version](https://img.shields.io/badge/Protect-v3.0-success?style=for-the-badge)
![Discord](https://img.shields.io/discord/122900397965705216?label=Discord&logo=Discord&logoColor=white&style=for-the-badge)

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

# Environment Configuration
php artisan key:generate --force
```

### 3. Finalization

Before finalizing, edit your `.env` file (`nano .env`) and configure your database details (using the password `YOUR_PASSWORD_HERE` you created earlier), Redis, and App URL. 

```bash
# Database setup and Permissions
php artisan view:clear && php artisan config:clear
php artisan migrate --seed --force

# Create First Admin User
php artisan p:user:make

# Set Permissions
chown -R www-data:www-data /var/www/pterodactyl/*
chown -R www-data:www-data /var/www/pterodactyl/.*
```

*(Note: Don't forget to configure your Nginx virtual host and crontab as per the standard Pterodactyl documentation).*

---

## Upgrading / Migrating from Official Pterodactyl

If you already have a working Pterodactyl panel installed and want to switch to **alxzen** without losing your data, database, or `.env` file, use the following sequence:

```bash
cd /var/www/pterodactyl
php artisan down

# Backup your existing .env and other crucial files
cp .env ../.env.backup
# Optional: backup the whole directory just in case
# cp -r /var/www/pterodactyl /var/www/pterodactyl_backup

# Remove old files (excluding storage and .env)
rm -rf app bootstrap config database public resources routes tests .editorconfig .env.example .eslintignore .eslintrc.js .gitattributes .gitignore .prettierignore .prettierrc artisan babel.config.js composer.json composer.lock jest.config.js package.json phpstan.neon postcss.config.js SECURITY.md tailwind.config.js tsconfig.json webpack.config.js yarn.lock

# Download and extract the alxzen release (assuming a release tar.gz is provided, or clone over it)
# Since alxzen is a full fork, you can download the tar.gz from the GitHub release tab:
curl -L https://github.com/alxzy-group/alxzen/releases/latest/download/panel.tar.gz | tar -xzv

# Ensure your .env is intact (it wasn't deleted in the rm command above, but just in case)
# cp ../.env.backup .env

# Install Dependencies
composer install --no-dev --optimize-autoloader
yarn install
yarn build:production

# Finalize
php artisan view:clear && php artisan config:clear
php artisan migrate --force
chown -R www-data:www-data /var/www/pterodactyl/*
chown -R www-data:www-data /var/www/pterodactyl/.*
php artisan up
php artisan queue:restart
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