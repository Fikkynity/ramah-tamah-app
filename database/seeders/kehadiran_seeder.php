<?php

/**
 * Seeder: Import data kehadiran dari file CSV.
 *
 * Format CSV:
 * nik
 *
 * Cara menjalankan:
 * php database/seeders/kehadiran_seeder.php
 *
 * Aman dijalankan berkali-kali.
 * Peserta yang sudah tercatat hadir akan dilewati.
 */

require_once dirname(__DIR__, 2) . '/config/database.php';

$csvPath = __DIR__ . '/kehadiran.csv';


// ========================================
// CEK FILE CSV
// ========================================

if (!file_exists($csvPath)) {
    fwrite(STDERR, "File tidak ditemukan: $csvPath\n");
    fwrite(STDERR, "Taruh kehadiran.csv di folder yang sama dengan skrip ini.\n");
    exit(1);
}


// ========================================
// DATABASE
// ========================================

$pdo = getDatabaseConnection();


// ========================================
// BUKA CSV
// ========================================

$handle = fopen($csvPath, 'r');

if ($handle === false) {
    fwrite(STDERR, "Gagal membuka file CSV.\n");
    exit(1);
}


// ========================================
// BACA HEADER
// ========================================

$header = fgetcsv($handle);


// ========================================
// QUERY
// ========================================

// Cari peserta berdasarkan NIK
$findPeserta = $pdo->prepare("
    SELECT id
    FROM ramah_tamah.peserta
    WHERE nik = :nik
");

// Insert kehadiran
$insertKehadiran = $pdo->prepare("
    INSERT INTO ramah_tamah.kehadiran
        (peserta_id, waktu_datang, status)
    VALUES
        (:peserta_id, NOW(), 'HADIR')
    ON CONFLICT (peserta_id) DO NOTHING
");


// ========================================
// COUNTER
// ========================================

$total = 0;
$inserted = 0;
$alreadyPresent = 0;
$notFound = 0;


// ========================================
// TRANSACTION
// ========================================

$pdo->beginTransaction();

try {

    while (($row = fgetcsv($handle)) !== false) {

        if (count($row) < 1) {
            continue;
        }

        $nik = trim($row[0]);

        if ($nik === '') {
            continue;
        }

        $total++;


        // Cari peserta berdasarkan NIK
        $findPeserta->execute([
            ':nik' => $nik
        ]);

        $peserta = $findPeserta->fetch();


        // NIK tidak ditemukan di tabel peserta
        if (!$peserta) {
            $notFound++;
            continue;
        }


        // Masukkan kehadiran
        $insertKehadiran->execute([
            ':peserta_id' => $peserta['id']
        ]);


        if ($insertKehadiran->rowCount() > 0) {
            $inserted++;
        } else {
            $alreadyPresent++;
        }
    }


    $pdo->commit();
} catch (Throwable $e) {

    $pdo->rollBack();

    fwrite(
        STDERR,
        "Gagal import: " . $e->getMessage() . "\n"
    );

    exit(1);
}


fclose($handle);


// ========================================
// HASIL
// ========================================

echo "Selesai.\n";
echo "Total NIK diproses       : $total\n";
echo "Berhasil ditambahkan    : $inserted\n";
echo "Sudah hadir             : $alreadyPresent\n";
echo "Peserta tidak ditemukan : $notFound\n";
