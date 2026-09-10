<?php

/**
 * Migration: Eksekusi DDL schema & tabel ramah_tamah.
 *
 * Cara pakai:
 * 1. Pastikan file create_table.sql ada di folder yang sama dengan file ini.
 * 2. Jalankan lewat terminal:
 *    php database/migrations/migrate.php
 *
 * Skrip ini AMAN dijalankan berkali-kali karena menggunakan
 * `CREATE SCHEMA IF NOT EXISTS` dan `CREATE TABLE` standar.
 */

require_once dirname(__DIR__, 2) . '/config/database.php';

$sqlPath = __DIR__ . '/create_table.sql';

if (!file_exists($sqlPath)) {
    fwrite(STDERR, "File tidak ditemukan: $sqlPath\n");
    fwrite(STDERR, "Taruh create_table.sql di folder yang sama dengan skrip ini.\n");
    exit(1);
}

$pdo = getDatabaseConnection();

$sql = file_get_contents($sqlPath);

if ($sql === false || trim($sql) === '') {
    fwrite(STDERR, "File SQL kosong atau tidak bisa dibaca.\n");
    exit(1);
}

try {
    // Eksekusi seluruh statement DDL di file SQL
    $pdo->exec($sql);

    echo "Migration selesai!\n";
    echo "Schema 'ramah_tamah' beserta tabel (peserta, kehadiran, hadiah, pemenang) berhasil dibuat.\n";
} catch (Throwable $e) {
    fwrite(STDERR, "Gagal menjalankan migration: " . $e->getMessage() . "\n");
    exit(1);
}
