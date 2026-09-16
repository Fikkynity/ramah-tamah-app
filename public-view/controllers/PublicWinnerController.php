<?php

// ========================================
// KONFIGURASI DATABASE
// ========================================

require_once dirname(__DIR__, 2) . '/config/database.php';


// ========================================
// PUBLIC WINNER LIST - READ ONLY
//
// PENTING: File ini SENGAJA hanya berisi
// query SELECT. Jangan pernah menambahkan
// fungsi untuk INSERT/UPDATE/DELETE di
// sini, karena file ini akan diakses dari
// internet (lewat tunnel) oleh ratusan/
// ribuan orang tanpa login.
//
// Kalau butuh fitur tambahan untuk halaman
// publik, pastikan tetap read-only.
//
// SARAN KEAMANAN TAMBAHAN:
// Ganti kredensial di config/database.php
// khusus untuk file ini dengan user MySQL/
// Postgres yang HANYA punya privilege
// SELECT (bukan user yang sama dengan
// aplikasi admin), supaya walau ada bug di
// masa depan, data tidak bisa diubah dari
// sini secara fisik di level database.
// ========================================

function getPublicWinnerList(): array
{
    $pdo = getDatabaseConnection();

    $stmt = $pdo->query("
        SELECT
            w.id,
            p.id AS peserta_id,
            p.nik,
            p.nama,
            p.departemen,
            h.nama_hadiah AS hadiah,
            w.waktu_menang,
            w.status_pengambilan,
            w.waktu_pengambilan

        FROM ramah_tamah.pemenang w

        INNER JOIN ramah_tamah.peserta p
            ON p.id = w.peserta_id

        INNER JOIN ramah_tamah.hadiah h
            ON h.id = w.hadiah_id

        ORDER BY w.waktu_menang DESC
    ");

    return $stmt->fetchAll();
}
