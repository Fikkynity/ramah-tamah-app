<?php

require_once dirname(__DIR__, 2) . '/config/database.php';

function login(string $nik, string $password): array
{
    $nik = trim($nik);
    $password = trim($password);

    if ($nik === '' || $password === '') {
        return [
            'success' => false,
            'type' => 'validation',
            'message' => 'NIK dan password harus diisi.'
        ];
    }

    if ($nik !== $password) {
        return [
            'success' => false,
            'type' => 'invalid_credentials',
            'message' => 'NIK atau password salah.'
        ];
    }

    $pdo = getDatabaseConnection();

    $stmt = $pdo->prepare("
        SELECT
            user_id,
            nik,
            nama,
            departemen,
            role,
            sudah_hadir,
            waktu_datang,
            is_pemenang,
            pemenang_id,
            hadiah,
            waktu_menang,
            status_pengambilan,
            waktu_pengambilan
        FROM ramah_tamah.v_user_dashboard
        WHERE nik = :nik
        LIMIT 1
    ");

    $stmt->execute([
        ':nik' => $nik
    ]);

    $user = $stmt->fetch();

    if (!$user) {
        return [
            'success' => false,
            'type' => 'invalid_credentials',
            'message' => 'NIK atau password salah.'
        ];
    }

    return [
        'success' => true,
        'type' => 'success',
        'message' => 'Login berhasil.',
        'user' => $user
    ];
}

function getUserDashboard(string $nik): array
{
    $nik = trim($nik);

    if ($nik === '') {
        return [
            'success' => false,
            'type' => 'validation',
            'message' => 'NIK harus diisi.'
        ];
    }

    $pdo = getDatabaseConnection();

    $stmt = $pdo->prepare("
        SELECT
            user_id,
            nik,
            nama,
            departemen,
            role,
            sudah_hadir,
            waktu_datang,
            is_pemenang,
            pemenang_id,
            hadiah,
            waktu_menang,
            status_pengambilan,
            waktu_pengambilan
        FROM ramah_tamah.v_user_dashboard
        WHERE nik = :nik
        LIMIT 1
    ");

    $stmt->execute([
        ':nik' => $nik
    ]);

    $user = $stmt->fetch();

    if (!$user) {
        return [
            'success' => false,
            'type' => 'not_found',
            'message' => 'User tidak ditemukan.'
        ];
    }

    return [
        'success' => true,
        'type' => 'success',
        'user' => $user
    ];
}

function getAdminStats(): array
{
    $pdo = getDatabaseConnection();

    $stmt = $pdo->query("
        SELECT
            (SELECT COUNT(*) FROM ramah_tamah.peserta) as total_peserta,
            (SELECT COUNT(*) FROM ramah_tamah.kehadiran) as total_hadir,
            (SELECT COUNT(*) FROM ramah_tamah.pemenang) as total_pemenang,
            (SELECT COUNT(*) FROM ramah_tamah.pemenang WHERE status_pengambilan = 'DIAMBIL') as total_hadiah_diambil,
            (SELECT COUNT(*) FROM ramah_tamah.hadiah) as total_jenis_hadiah
    ");

    $stats = $stmt->fetch();

    return [
        'success' => true,
        'type' => 'success',
        'stats' => $stats
    ];
}
