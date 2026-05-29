#!/bin/bash
# ==============================================================================
# alxzen Panel — Post-Install / Post-Upgrade Setup Script
# Jalankan: bash setup.sh
# ==============================================================================

RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'
BLUE='\033[0;34m'; CYAN='\033[0;36m'; BOLD='\033[1m'; NC='\033[0m'

print_header() {
    echo -e "\n${BLUE}${BOLD}╔══════════════════════════════════════════════╗${NC}"
    echo -e "${BLUE}${BOLD}║        alxzen Panel — Setup Script           ║${NC}"
    echo -e "${BLUE}${BOLD}╚══════════════════════════════════════════════╝${NC}\n"
}

print_step() { echo -e "${CYAN}${BOLD}[STEP $1]${NC} $2"; }
print_ok()   { echo -e "  ${GREEN}✓${NC} $1"; }
print_warn() { echo -e "  ${YELLOW}⚠${NC}  $1"; }
print_err()  { echo -e "  ${RED}✗${NC} $1"; }
print_ask()  { echo -e "  ${YELLOW}?${NC}  $1"; }

PANEL_DIR="$(cd "$(dirname "$0")" && pwd)"
ENV_FILE="$PANEL_DIR/.env"

print_header

# ── STEP 1: Pastikan .env ada ─────────────────────────────────────────────────
print_step 1 "Memeriksa file .env ..."
if [ ! -f "$ENV_FILE" ]; then
    print_warn ".env tidak ditemukan, membuat dari .env.example ..."
    cp "$PANEL_DIR/.env.example" "$ENV_FILE"
    print_ok ".env dibuat. Edit file ini dulu sebelum lanjut!"
    echo ""
    echo -e "  ${RED}PENTING: Atur nilai berikut di .env:${NC}"
    echo -e "    ${BOLD}APP_URL${NC}=https://domain-panel-kamu.com"
    echo -e "    ${BOLD}DB_HOST${NC}, ${BOLD}DB_DATABASE${NC}, ${BOLD}DB_USERNAME${NC}, ${BOLD}DB_PASSWORD${NC}"
    echo ""
    read -p "  Sudah edit .env? (y/N): " CONFIRM
    [[ "$CONFIRM" != "y" && "$CONFIRM" != "Y" ]] && { print_err "Batalkan. Edit .env dulu!"; exit 1; }
else
    print_ok ".env ditemukan"
fi

# ── STEP 2: Validasi APP_URL ──────────────────────────────────────────────────
print_step 2 "Memvalidasi APP_URL ..."
APP_URL=$(grep -E "^APP_URL=" "$ENV_FILE" | cut -d'=' -f2- | tr -d '"' | tr -d "'")
if [ -z "$APP_URL" ] || [ "$APP_URL" = "http://localhost" ] || [ "$APP_URL" = "http://example.com" ]; then
    print_err "APP_URL belum diatur dengan benar! Nilai saat ini: '${APP_URL}'"
    echo ""
    print_ask "Masukkan URL panel kamu (contoh: https://panel.domain.com):"
    read -p "  APP_URL=" NEW_URL
    if [ -z "$NEW_URL" ]; then
        print_err "URL tidak boleh kosong!"; exit 1
    fi
    # Update APP_URL di .env
    sed -i "s|^APP_URL=.*|APP_URL=${NEW_URL}|" "$ENV_FILE"
    APP_URL="$NEW_URL"
    print_ok "APP_URL diperbarui ke: $APP_URL"
else
    print_ok "APP_URL: $APP_URL"
fi

# ── STEP 3: Cek APP_KEY ───────────────────────────────────────────────────────
print_step 3 "Memeriksa APP_KEY ..."
APP_KEY=$(grep -E "^APP_KEY=" "$ENV_FILE" | cut -d'=' -f2-)
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "SomeRandomString" ]; then
    print_warn "APP_KEY kosong, men-generate ..."
    php artisan key:generate --force
    print_ok "APP_KEY berhasil di-generate"
else
    print_ok "APP_KEY sudah ada"
fi

# ── STEP 4: Bersihkan semua cache (KRITIS untuk WebSocket) ───────────────────
print_step 4 "Membersihkan cache lama (penting untuk WebSocket) ..."
php "$PANEL_DIR/artisan" config:clear  > /dev/null 2>&1
php "$PANEL_DIR/artisan" cache:clear   > /dev/null 2>&1
php "$PANEL_DIR/artisan" route:clear   > /dev/null 2>&1
php "$PANEL_DIR/artisan" view:clear    > /dev/null 2>&1
php "$PANEL_DIR/artisan" event:clear   > /dev/null 2>&1
print_ok "Semua cache dibersihkan"

# ── STEP 5: Jalankan migrasi database ────────────────────────────────────────
print_step 5 "Menjalankan migrasi database ..."
php "$PANEL_DIR/artisan" migrate --force
if [ $? -ne 0 ]; then
    print_err "Migrasi gagal! Cek koneksi database di .env"
    exit 1
fi
print_ok "Migrasi selesai"

# ── STEP 6: Set permissions ───────────────────────────────────────────────────
print_step 6 "Mengatur permissions file ..."
chown -R www-data:www-data "$PANEL_DIR"/* 2>/dev/null
chown -R www-data:www-data "$PANEL_DIR"/.[!.]* 2>/dev/null
chmod -R 755 "$PANEL_DIR/storage" "$PANEL_DIR/bootstrap/cache"
print_ok "Permissions selesai"

# ── STEP 7: Build cache baru ──────────────────────────────────────────────────
print_step 7 "Membangun cache baru ..."
php "$PANEL_DIR/artisan" config:cache   > /dev/null 2>&1
php "$PANEL_DIR/artisan" route:cache    > /dev/null 2>&1
php "$PANEL_DIR/artisan" view:cache     > /dev/null 2>&1
php "$PANEL_DIR/artisan" event:cache    > /dev/null 2>&1
print_ok "Cache berhasil dibangun"

# ── STEP 8: Restart queue worker ─────────────────────────────────────────────
print_step 8 "Merestart queue worker ..."
php "$PANEL_DIR/artisan" queue:restart  > /dev/null 2>&1
print_ok "Queue worker direstart"

# ── STEP 9: Nyalakan kembali panel ───────────────────────────────────────────
print_step 9 "Menyalakan panel ..."
php "$PANEL_DIR/artisan" up > /dev/null 2>&1
print_ok "Panel aktif"

# ── STEP 10: Reminder Wings ───────────────────────────────────────────────────
print_step 10 "Pengingat konfigurasi Wings ..."
echo ""
echo -e "  ${YELLOW}${BOLD}PENTING — Jika kamu baru install atau pindah domain:${NC}"
echo -e "  Wings harus di-reconfigure agar WebSocket berfungsi."
echo ""
echo -e "  ${BOLD}Lakukan di server node (Wings):${NC}"
echo -e "    1. Buka: ${CYAN}${APP_URL}/admin/nodes${NC}"
echo -e "    2. Klik node → tab 'Configuration'"
echo -e "    3. Klik 'Generate Token' → copy perintah yang muncul"
echo -e "    4. Jalankan perintah itu di server Wings"
echo -e "    5. Jalankan: ${BOLD}systemctl restart wings${NC}"
echo ""
echo -e "  ${GREEN}Jika Wings sudah terpasang di VPS YANG SAMA (localhost):${NC}"
echo -e "  Panel URL tidak berubah → WebSocket langsung jalan tanpa reconfigure."
echo ""

# ── SELESAI ───────────────────────────────────────────────────────────────────
echo -e "${GREEN}${BOLD}╔══════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}${BOLD}║      ✓  Setup alxzen selesai!               ║${NC}"
echo -e "${GREEN}${BOLD}╚══════════════════════════════════════════════╝${NC}"
echo -e ""
echo -e "  Panel URL: ${CYAN}${BOLD}${APP_URL}${NC}"
echo -e ""
