<?php

/**
 * Seeder: Import master data peserta dari file CSV.
 *
 * Cara pakai:
 * 1. Pastikan file peserta.csv ada di folder yang sama dengan file ini
 *    (database/seeders/peserta.csv), dengan format kolom:
 *    nik,nama,departemen
 * 2. Jalankan lewat terminal:
 *    php database/seeders/peserta_seeder.php
 *
 * Skrip ini AMAN dijalankan berkali-kali: NIK yang sudah ada
 * akan dilewati (tidak dobel), bukan bikin error.
 */

require_once dirname(__DIR__, 2) . '/config/database.php';

$csvPath = __DIR__ . '/peserta.csv';

if (!file_exists($csvPath)) {
    fwrite(STDERR, "File tidak ditemukan: $csvPath\n");
    fwrite(STDERR, "Taruh peserta.csv di folder yang sama dengan skrip ini.\n");
    exit(1);
}

$pdo = getDatabaseConnection();

/*
 * Pastikan ada UNIQUE constraint di kolom nik.
 * Ini wajib supaya ON CONFLICT (nik) di bawah bisa jalan.
 * Aman dijalankan berkali-kali (IF NOT EXISTS).
 */
$pdo->exec("
    DO $$
    BEGIN
        IF NOT EXISTS (
            SELECT 1 FROM pg_constraint
            WHERE conname = 'peserta_nik_unique'
        ) THEN
            ALTER TABLE ramah_tamah.peserta
                ADD CONSTRAINT peserta_nik_unique UNIQUE (nik);
        END IF;
    END $$;
");

$handle = fopen($csvPath, 'r');

if ($handle === false) {
    fwrite(STDERR, "Gagal membuka file CSV.\n");
    exit(1);
}

// Baca header (nik,nama,departemen) dan abaikan
$header = fgetcsv($handle);

$stmt = $pdo->prepare("
    INSERT INTO ramah_tamah.peserta (nik, nama, departemen)
    VALUES (:nik, :nama, :departemen)
    ON CONFLICT (nik) DO NOTHING
");

$total = 0;
$inserted = 0;

$pdo->beginTransaction();

try {
    while (($row = fgetcsv($handle)) !== false) {

        if (count($row) < 3) {
            continue;
        }

        [$nik, $nama, $departemen] = $row;

        $nik = trim($nik);
        $nama = trim($nama);
        $departemen = trim($departemen);

        if ($nik === '' || $nama === '') {
            continue;
        }

        $stmt->execute([
            ':nik' => $nik,
            ':nama' => $nama,
            ':departemen' => $departemen,
        ]);

        $total++;

        if ($stmt->rowCount() > 0) {
            $inserted++;
        }
    }

    $pdo->commit();
} catch (Throwable $e) {
    $pdo->rollBack();
    fwrite(STDERR, "Gagal import: " . $e->getMessage() . "\n");
    exit(1);
}

fclose($handle);

echo "Selesai.\n";
echo "Total baris diproses : $total\n";
echo "Berhasil ditambahkan : $inserted\n";
echo "Dilewati (NIK sudah ada) : " . ($total - $inserted) . "\n";
