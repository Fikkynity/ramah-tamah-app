# Event Management & Lucky Draw System

A native PHP application with custom MVC-like architecture for managing event participants, attendance, lucky draws, and prize claims. Built with PostgreSQL supporting custom schemas (`ramah_tamah`) and Bootstrap UI.

---

## 📋 Table of Contents

- [Prerequisites](#-prerequisites)
- [Project Structure](#-project-structure)
- [Installation & Setup](#-installation--setup)
  - [Step 1: Install Dependencies](#step-1-install-dependencies)
  - [Step 2: Create PostgreSQL Database & Schema](#step-2-create-postgresql-database--schema)
  - [Step 3: Environment Configuration](#step-3-environment-configuration)
  - [Step 4: Run Database Migrations](#step-4-run-database-migrations)
  - [Step 5: Seed Initial Data](#step-5-seed-initial-data)
- [Database Schema Overview](#-database-schema-overview)
- [Troubleshooting](#-troubleshooting)
- [License](#-license)

---

## ⚡ Prerequisites

Ensure you have the following installed on your system:

- **PHP** `>= 8.0` with `pdo_pgsql` extension enabled
- **Node.js** `>= 16.0` & **NPM**
- **PostgreSQL** `>= 12.0`
- **Composer** (for PHP dependency management)

---

## 📁 Project Structure

```
.
├── config/                  # Database and core application configurations
├── database/
│   ├── migrations/          # DDL files and migration runner scripts
│   └── seeders/              # CSV data and seeder runner scripts
├── public/                  # Public web root (CSS, JS, Fonts assets)
├── src/
│   ├── controllers/          # Application logic controllers
│   ├── css/                  # Source CSS / styling files
│   ├── js/                   # Source JavaScript files
│   └── views/                 # Application view components & layouts
├── .env.example              # Sample environment configuration
├── package.json              # NPM package management
├── composer.json             # PHP package management
└── README.md                 # Project documentation
```

---

## 🚀 Installation & Setup

Follow these step-by-step instructions to initialize and run your application.

### Step 1: Install Dependencies

1. Clone the repository and navigate into the project root:

   ```bash
   git clone https://github.com/Fikknity/ramah-tamah-app.git
   cd ramah-tamah-app
   ```

2. Install Node.js dependencies:

   ```bash
   npm install
   ```

3. Install PHP dependencies via Composer:
   ```bash
   composer install
   ```

### Step 2: Create PostgreSQL Database & Schema

Open your PostgreSQL CLI (`psql`) or database GUI (e.g., DBeaver, pgAdmin) and create the database along with the custom `ramah_tamah` schema:

```sql
-- Create database
CREATE DATABASE ramah_tamah_db;

-- Connect to database
\c ramah_tamah_db

-- Create custom schema
CREATE SCHEMA IF NOT EXISTS ramah_tamah;
```

### Step 3: Environment Configuration

1. Copy `.env.example` to create your local `.env`:

   ```bash
   cp .env.example .env
   ```

2. Open `.env` and configure your PostgreSQL database credentials:
   ```
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=ramah_tamah_db
   DB_USERNAME=postgres
   DB_PASSWORD=your_secure_password
   ```

### Step 4: Run Database Migrations

Execute the migration script to build tables, constraints, and relationships in the `ramah_tamah` schema:

```bash
php database/migrations/migrate.php
```

### Step 5: Seed Initial Data

Import the participant master dataset from CSV into the database:

```bash
php database/seeders/peserta_seeder.php
```

> **Note:** The seeder is idempotent and safe to run multiple times (`ON CONFLICT (nik) DO NOTHING`).

---

## 🗄️ Database Schema Overview

All application entities are organized under the `ramah_tamah` schema:

| Table                   | Description                       | Key Features                                      |
| ----------------------- | --------------------------------- | ------------------------------------------------- |
| `ramah_tamah.peserta`   | Master list of event participants | Unique `nik`, indexed names & departments         |
| `ramah_tamah.kehadiran` | Participant attendance logs       | One-to-one link to `peserta`, default `'HADIR'`   |
| `ramah_tamah.hadiah`    | Lucky draw prize inventory        | Positive quantity check constraint (`jumlah > 0`) |
| `ramah_tamah.pemenang`  | Winners and prize claim tracking  | Status flow: `'MENANG'` → `'DIAMBIL'`             |

---

## 🛠️ Troubleshooting

**`driver not found` Error:**
Ensure the PostgreSQL extension is enabled in your `php.ini`:

```ini
extension=pdo_pgsql
```

**Schema Access Permission:**
Ensure the database user has sufficient rights:

```sql
GRANT ALL ON SCHEMA ramah_tamah TO your_username;
```

---

## 📄 License

Proprietary - All Rights Reserved.
