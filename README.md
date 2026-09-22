# Event Management & Lucky Draw System

Aplikasi PHP native untuk mengelola peserta acara, kehadiran, lucky draw, dan pengambilan hadiah. Aplikasi memakai PostgreSQL dengan schema `ramah_tamah` dan Bootstrap.

## Prasyarat

- PHP `>= 8.0` dengan ekstensi `pdo_pgsql`
- Node.js `>= 16.0` dan npm
- PostgreSQL `>= 12.0`
- Apache dan PHP jika dijalankan melalui XAMPP

Composer tidak diperlukan. Proyek ini tidak memakai `composer.json` atau dependency PHP eksternal.

## Struktur Project

```text
.
├── config/                  # Konfigurasi database
├── database/
│   ├── migrations/          # SQL dan script migration
│   └── seeders/              # Data awal peserta
├── public/                  # Web root dan entry point aplikasi
├── src/
│   ├── controllers/          # Logic aplikasi
│   ├── css/                  # Source CSS
│   ├── js/                   # Source JavaScript
│   └── views/                # View dan layout
├── assets/                  # Asset pendukung
├── .env.example              # Template konfigurasi lokal
├── package.json              # Dependency frontend
└── README.md
```

## Instalasi

### 1. Clone dan install dependency frontend

```bash
git clone https://github.com/Fikkynity/ramah-tamah-app.git
cd ramah-tamah-app
npm install
```

### 2. Buat database PostgreSQL

Gunakan `psql`, pgAdmin, atau DBeaver:

```sql
CREATE DATABASE ramah_tamah;
```

Setelah terhubung ke database `ramah_tamah`, buat schema:

```sql
CREATE SCHEMA IF NOT EXISTS ramah_tamah;
```

### 3. Buat file environment

Salin `.env.example` menjadi `.env`:

```bash
cp .env.example .env
```

Lalu isi kredensial PostgreSQL:

```env
APP_ENV=local
APP_DEBUG=true

DB_HOST=127.0.0.1
DB_PORT=5432
DB_NAME=ramah_tamah
DB_USER=postgres
DB_PASSWORD=your_secure_password
```

`DB_NAME` dan `DB_USER` harus memakai nama variable tersebut. Aplikasi membacanya dari `config/database.php`.

### 4. Jalankan migration utama

```bash
php database/migrations/migrate.php
```

Script tersebut menjalankan `database/migrations/create_table.sql` untuk membuat tabel utama.

### 5. Jalankan migration users

Migration users masih berupa file SQL terpisah. Jalankan setelah migration utama:

```bash
psql -U postgres -d ramah_tamah -f database/migrations/create_users_table.sql
```

Jika diminta password, masukkan password PostgreSQL pada `.env`.

Migration ini membuat tabel `ramah_tamah.users`, view `ramah_tamah.v_user_dashboard`, trigger timestamp, dan data user awal.

### 6. Seed data peserta

```bash
php database/seeders/peserta_seeder.php
```

Seeder aman dijalankan berulang kali karena memakai `ON CONFLICT (nik) DO NOTHING`.

### 7. Jalankan aplikasi

Dengan XAMPP, aktifkan Apache lalu buka:

```text
http://localhost/ramah-tamah-app/public/
```

Untuk PHP built-in server:

```bash
php -S localhost:8000 -t public
```

Lalu buka `http://localhost:8000`.

## API Endpoint

Base URL:

```text
http://localhost/ramah-tamah-app/public/index.php
```

| Method | Action | Endpoint | Body / Query |
|---|---|---|---|
| POST | Login | `?action=login` | `{ "nik": "...", "password": "..." }` |
| GET | User dashboard | `?action=user-dashboard&nik=...` | `nik` |
| GET | Admin stats | `?action=admin-stats` | - |
| POST | Scan attendance | `?action=scan-attendance` | `{ "nik": "..." }` |
| POST | Scan prize pickup | `?action=scan-prize` | `{ "nik": "..." }` |
| GET | Winners pagination | `?action=winners-paginated&page=1&per_page=50` | `page`, `per_page` |
| GET | Users list | `?action=users-list&page=1&per_page=50` | `search`, `role`, `page`, `per_page` |
| POST | Update user role | `?action=update-user-role` | `{ "nik": "...", "role": "user" }` |
| POST | Attendance web | `?action=attendance` | `{ "nik": "..." }` |
| GET | Attendance list | `?action=attendance-list` | optional `m=1` |
| POST | Draw winner | `?action=draw-winner` | `{ "hadiah": "...", "m": "0" }` |
| POST | Draw winner batch | `?action=draw-winner-batch` | `{ "hadiah": "...", "jumlah": 1, "m": "0" }` |
| GET | Winner list | `?action=winner-list` | - |
| POST | Check prize | `?action=check-prize` | `{ "nik": "..." }` |
| POST | Take prize | `?action=take-prize` | `{ "pemenang_id": 1 }` |

Contoh login:

```bash
curl -X POST "http://localhost/ramah-tamah-app/public/index.php?action=login" \
  -H "Content-Type: application/json" \
  -d '{"nik":"2022-001931","password":"2022-001931"}'
```

## User Awal

Migration users membuat admin awal:

```text
NIK      : 2022-001931
Password : 2022-001931
Role     : admin
```

Password saat ini divalidasi sama dengan NIK oleh endpoint login.

## Android Emulator

Project Android memakai backend ini melalui:

```text
http://10.0.2.2/ramah-tamah-app/public/
```

`10.0.2.2` menunjuk ke localhost komputer dari Android Emulator. Pastikan Apache, PostgreSQL, database, dan migration sudah aktif.

## Database Schema

Semua entity berada di schema `ramah_tamah`:

| Table / View | Deskripsi |
|---|---|
| `ramah_tamah.peserta` | Master peserta acara |
| `ramah_tamah.kehadiran` | Catatan kehadiran peserta |
| `ramah_tamah.hadiah` | Inventaris hadiah |
| `ramah_tamah.pemenang` | Pemenang dan status pengambilan hadiah |
| `ramah_tamah.users` | Akun peserta dan admin |
| `ramah_tamah.v_user_dashboard` | Data dashboard user |

## Troubleshooting

### `driver not found`

Aktifkan ekstensi PostgreSQL pada `php.ini`:

```ini
extension=pdo_pgsql
```

Restart Apache setelah mengubah `php.ini`.

### `relation does not exist`

Pastikan migration dijalankan berurutan:

```bash
php database/migrations/migrate.php
psql -U postgres -d ramah_tamah -f database/migrations/create_users_table.sql
php database/seeders/peserta_seeder.php
```

### Permission schema

Berikan permission jika user PostgreSQL tidak memiliki akses:

```sql
GRANT ALL ON SCHEMA ramah_tamah TO postgres;
GRANT ALL ON ALL TABLES IN SCHEMA ramah_tamah TO postgres;
GRANT ALL ON ALL SEQUENCES IN SCHEMA ramah_tamah TO postgres;
```

## License

Proprietary - All Rights Reserved.
