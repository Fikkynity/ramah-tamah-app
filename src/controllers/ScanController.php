<?php

require_once dirname(__DIR__, 2) . '/config/database.php';

/**
 * Scan Kehadiran Peserta (untuk Admin)
 * Mencatat peserta hadir berdasarkan NIK
 */
function scanAttendance(string $nik): array
{
    $nik = trim($nik);

    if ($nik === '') {
        return [
            'success' => false,
            'type' => 'validation',
            'message' => 'NIK tidak boleh kosong.'
        ];
    }

    $pdo = getDatabaseConnection();

    try {
        $pdo->beginTransaction();

        // Cari peserta
        $stmt = $pdo->prepare("
            SELECT id, nik, nama, departemen
            FROM ramah_tamah.peserta
            WHERE nik = :nik
            LIMIT 1
        ");
        $stmt->execute([':nik' => $nik]);
        $peserta = $stmt->fetch();

        if (!$peserta) {
            $pdo->rollBack();
            return [
                'success' => false,
                'type' => 'not_found',
                'message' => 'Peserta tidak ditemukan.'
            ];
        }

        // Cek apakah sudah hadir
        $stmt = $pdo->prepare("
            SELECT id, waktu_datang
            FROM ramah_tamah.kehadiran
            WHERE peserta_id = :peserta_id
            LIMIT 1
        ");
        $stmt->execute([':peserta_id' => $peserta['id']]);
        $existing = $stmt->fetch();

        if ($existing) {
            $pdo->rollBack();
            return [
                'success' => false,
                'type' => 'already_attended',
                'message' => 'Peserta sudah tercatat hadir sebelumnya.',
                'participant' => $peserta,
                'waktu_datang' => $existing['waktu_datang']
            ];
        }

        // Insert kehadiran
        $stmt = $pdo->prepare("
            INSERT INTO ramah_tamah.kehadiran (peserta_id, waktu_datang, status)
            VALUES (:peserta_id, NOW(), 'HADIR')
            RETURNING waktu_datang
        ");
        $stmt->execute([':peserta_id' => $peserta['id']]);
        $result = $stmt->fetch();

        $pdo->commit();

        return [
            'success' => true,
            'type' => 'success',
            'message' => 'Kehadiran berhasil dicatat.',
            'participant' => $peserta,
            'waktu_datang' => $result['waktu_datang']
        ];

    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}

/**
 * Scan Pengambilan Hadiah (untuk Admin)
 * Mencatat hadiah sudah diambil berdasarkan NIK pemenang
 */
function scanPrizePickup(string $nik): array
{
    $nik = trim($nik);

    if ($nik === '') {
        return [
            'success' => false,
            'type' => 'validation',
            'message' => 'NIK tidak boleh kosong.'
        ];
    }

    $pdo = getDatabaseConnection();

    try {
        $pdo->beginTransaction();

        // Cari pemenang dengan NIK
        $stmt = $pdo->prepare("
            SELECT 
                w.id as pemenang_id,
                w.peserta_id,
                w.hadiah_id,
                w.status_pengambilan,
                w.waktu_pengambilan,
                p.nik,
                p.nama,
                p.departemen,
                h.nama_hadiah
            FROM ramah_tamah.pemenang w
            INNER JOIN ramah_tamah.peserta p ON p.id = w.peserta_id
            INNER JOIN ramah_tamah.hadiah h ON h.id = w.hadiah_id
            WHERE p.nik = :nik
            LIMIT 1
            FOR UPDATE
        ");
        $stmt->execute([':nik' => $nik]);
        $winner = $stmt->fetch();

        if (!$winner) {
            $pdo->rollBack();
            return [
                'success' => false,
                'type' => 'not_winner',
                'message' => 'Peserta bukan pemenang Lucky Draw.'
            ];
        }

        if ($winner['status_pengambilan'] === 'DIAMBIL') {
            $pdo->rollBack();
            return [
                'success' => false,
                'type' => 'already_taken',
                'message' => 'Hadiah sudah pernah diambil.',
                'winner' => [
                    'id' => $winner['pemenang_id'],
                    'nik' => $winner['nik'],
                    'nama' => $winner['nama'],
                    'departemen' => $winner['departemen'],
                    'hadiah' => $winner['nama_hadiah'],
                    'waktu_pengambilan' => $winner['waktu_pengambilan']
                ]
            ];
        }

        // Update status pengambilan
        $stmt = $pdo->prepare("
            UPDATE ramah_tamah.pemenang
            SET status_pengambilan = 'DIAMBIL',
                waktu_pengambilan = NOW()
            WHERE id = :id
            RETURNING waktu_pengambilan
        ");
        $stmt->execute([':id' => $winner['pemenang_id']]);
        $result = $stmt->fetch();

        $pdo->commit();

        return [
            'success' => true,
            'type' => 'success',
            'message' => 'Pengambilan hadiah berhasil dikonfirmasi.',
            'winner' => [
                'id' => $winner['pemenang_id'],
                'nik' => $winner['nik'],
                'nama' => $winner['nama'],
                'departemen' => $winner['departemen'],
                'hadiah' => $winner['nama_hadiah'],
                'waktu_pengambilan' => $result['waktu_pengambilan']
            ]
        ];

    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}

/**
 * Get daftar pemenang lengkap dengan pagination
 */
function getWinnersList(int $page = 1, int $perPage = 50): array
{
    $pdo = getDatabaseConnection();
    $offset = ($page - 1) * $perPage;

    // Total count
    $countStmt = $pdo->query("SELECT COUNT(*) as total FROM ramah_tamah.pemenang");
    $total = $countStmt->fetch()['total'];

    // Data pemenang
    $stmt = $pdo->prepare("
        SELECT 
            w.id,
            w.peserta_id,
            p.nik,
            p.nama,
            p.departemen,
            h.nama_hadiah as hadiah,
            w.waktu_menang,
            w.status_pengambilan,
            w.waktu_pengambilan
        FROM ramah_tamah.pemenang w
        INNER JOIN ramah_tamah.peserta p ON p.id = w.peserta_id
        INNER JOIN ramah_tamah.hadiah h ON h.id = w.hadiah_id
        ORDER BY w.waktu_menang DESC
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $winners = $stmt->fetchAll();

    return [
        'success' => true,
        'data' => $winners,
        'pagination' => [
            'page' => $page,
            'per_page' => $perPage,
            'total' => (int)$total,
            'total_pages' => ceil($total / $perPage)
        ]
    ];
}
