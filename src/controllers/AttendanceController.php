<?php

require_once dirname(__DIR__, 2) . '/config/database.php';


// ============================================================
// ATTENDANCE
// ============================================================

function verifyAttendance(string $nik): array
{
    $pdo = getDatabaseConnection();

    // Cari peserta
    $stmt = $pdo->prepare("
        SELECT
            id,
            nik,
            nama,
            departemen
        FROM ramah_tamah.peserta
        WHERE nik = :nik
        LIMIT 1
    ");

    $stmt->execute([
        ':nik' => $nik
    ]);

    $peserta = $stmt->fetch();

    if (!$peserta) {
        return [
            'success' => false,
            'type' => 'not_found',
            'message' => 'Peserta tidak ditemukan.'
        ];
    }


    // Cek apakah sudah hadir
    $stmt = $pdo->prepare("
        SELECT
            waktu_datang
        FROM ramah_tamah.kehadiran
        WHERE peserta_id = :peserta_id
        LIMIT 1
    ");

    $stmt->execute([
        ':peserta_id' => $peserta['id']
    ]);

    $kehadiran = $stmt->fetch();

    if ($kehadiran) {
        return [
            'success' => false,
            'type' => 'already_attended',
            'message' => 'Peserta sudah melakukan scan.',
            'participant' => $peserta,
            'waktu_datang' => $kehadiran['waktu_datang']
        ];
    }


    // Simpan kehadiran
    $stmt = $pdo->prepare("
        INSERT INTO ramah_tamah.kehadiran (
            peserta_id,
            waktu_datang,
            status
        )
        VALUES (
            :peserta_id,
            NOW(),
            'HADIR'
        )
        RETURNING waktu_datang
    ");

    $stmt->execute([
        ':peserta_id' => $peserta['id']
    ]);

    $result = $stmt->fetch();


    return [
        'success' => true,
        'type' => 'success',
        'message' => 'Peserta berhasil dicatat hadir.',
        'participant' => $peserta,
        'waktu_datang' => $result['waktu_datang']
    ];
}


/**
 * Ambil seluruh daftar kehadiran.
 * Mendukung filter khusus karyawan tetap (isTetap = true).
 */
function getAttendanceList(bool $isTetap = false): array
{
    $pdo = getDatabaseConnection();

    $tableSource = $isTetap ? 'ramah_tamah.v_kehadiran_tetap' : 'ramah_tamah.kehadiran';

    $stmt = $pdo->query("
        SELECT
            k.id,
            p.id AS peserta_id,
            p.nik,
            p.nama,
            p.departemen,
            k.waktu_datang,
            k.status
        FROM {$tableSource} k
        INNER JOIN ramah_tamah.peserta p
            ON p.id = k.peserta_id
        ORDER BY k.waktu_datang DESC
    ");

    return $stmt->fetchAll();
}


// ============================================================
// LUCKY DRAW - HELPER
// ============================================================

/**
 * Cari atau buat hadiah berdasarkan nama.
 *
 * Kolom jumlah tidak digunakan sebagai pembatas
 * jumlah pemenang pada sistem Lucky Draw.
 */
function getOrCreatePrize(PDO $pdo, string $namaHadiah): array
{
    $stmt = $pdo->prepare("
        SELECT
            id,
            nama_hadiah,
            jumlah
        FROM ramah_tamah.hadiah
        WHERE nama_hadiah = :nama_hadiah
        LIMIT 1
    ");

    $stmt->execute([
        ':nama_hadiah' => $namaHadiah
    ]);

    $hadiah = $stmt->fetch();

    if ($hadiah) {
        return $hadiah;
    }


    // Hadiah belum ada → buat otomatis
    $stmt = $pdo->prepare("
        INSERT INTO ramah_tamah.hadiah (
            nama_hadiah,
            jumlah
        )
        VALUES (
            :nama_hadiah,
            1
        )
        RETURNING
            id,
            nama_hadiah,
            jumlah
    ");

    $stmt->execute([
        ':nama_hadiah' => $namaHadiah
    ]);

    return $stmt->fetch();
}


// ============================================================
// LUCKY DRAW - MANUAL
// ============================================================

/**
 * Manual Draw
 *
 * Mengambil tepat 1 pemenang.
 */
function drawWinner(string $namaHadiah, bool $isTetap = false): array
{
    $namaHadiah = trim($namaHadiah);

    if ($namaHadiah === '') {
        return [
            'success' => false,
            'type' => 'validation',
            'message' => 'Hadiah belum diisi.'
        ];
    }


    $pdo = getDatabaseConnection();


    try {

        $pdo->beginTransaction();


        /*
         * Kunci proses Lucky Draw.
         *
         * Tujuannya agar dua proses draw yang berjalan
         * bersamaan tidak mengambil peserta yang sama.
         *
         * Lock ini hanya berlaku selama transaksi.
         */
        $pdo->exec("
            SELECT pg_advisory_xact_lock(
                hashtext('lucky_draw')
            )
        ");


        // Tentukan sumber tabel kehadiran
        $tableSource = $isTetap ? 'ramah_tamah.v_kehadiran_tetap' : 'ramah_tamah.kehadiran';

        // Cari peserta yang:
        // 1. Sudah hadir (dari sumber yang sesuai)
        // 2. Belum pernah menang
        $stmt = $pdo->prepare("
            SELECT
                p.id,
                p.nik,
                p.nama,
                p.departemen
            FROM ramah_tamah.peserta p

            INNER JOIN {$tableSource} k
                ON k.peserta_id = p.id

            LEFT JOIN ramah_tamah.pemenang w
                ON w.peserta_id = p.id

            WHERE w.id IS NULL

            ORDER BY RANDOM()

            LIMIT 1
        ");

        $stmt->execute();

        $peserta = $stmt->fetch();


        if (!$peserta) {

            $pdo->rollBack();

            return [
                'success' => false,
                'type' => 'no_participant',
                'message' => 'Tidak ada peserta yang dapat diundi.'
            ];
        }


        // Cari / buat hadiah
        $hadiah = getOrCreatePrize(
            $pdo,
            $namaHadiah
        );


        // Simpan pemenang
        $stmt = $pdo->prepare("
            INSERT INTO ramah_tamah.pemenang (
                peserta_id,
                hadiah_id,
                waktu_menang,
                status_pengambilan
            )
            VALUES (
                :peserta_id,
                :hadiah_id,
                NOW(),
                'MENANG'
            )
            RETURNING
                id,
                waktu_menang
        ");

        $stmt->execute([
            ':peserta_id' => $peserta['id'],
            ':hadiah_id' => $hadiah['id']
        ]);

        $winner = $stmt->fetch();


        $pdo->commit();


        return [
            'success' => true,
            'type' => 'success',
            'message' => 'Pemenang berhasil dipilih.',
            'winner' => [
                'id' => $winner['id'],
                'peserta_id' => $peserta['id'],
                'nik' => $peserta['nik'],
                'nama' => $peserta['nama'],
                'departemen' => $peserta['departemen'],
                'hadiah' => $hadiah['nama_hadiah'],
                'waktu_menang' => $winner['waktu_menang']
            ]
        ];
    } catch (Throwable $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        throw $e;
    }
}


// ============================================================
// LUCKY DRAW - AUTO
// ============================================================

/**
 * Auto Draw
 *
 * Mengambil N pemenang SEKALIGUS.
 *
 * Contoh:
 *
 * jumlahPemenang = 30
 *
 * Maka database akan memilih 30 peserta
 * dan memasukkan 30 pemenang dalam satu transaksi.
 *
 * Interval animasi TIDAK ditangani di sini.
 */
function drawWinnerBatch(
    string $namaHadiah,
    int $jumlahPemenang,
    bool $isTetap = false
): array {

    $namaHadiah = trim($namaHadiah);


    // Validasi hadiah
    if ($namaHadiah === '') {
        return [
            'success' => false,
            'type' => 'validation',
            'message' => 'Hadiah belum diisi.'
        ];
    }


    // Validasi jumlah pemenang
    if ($jumlahPemenang < 1) {
        return [
            'success' => false,
            'type' => 'validation',
            'message' => 'Jumlah pemenang minimal 1.'
        ];
    }


    $pdo = getDatabaseConnection();


    try {

        $pdo->beginTransaction();


        /*
         * Pastikan hanya satu Lucky Draw
         * yang berjalan dalam satu waktu.
         */
        $pdo->exec("
            SELECT pg_advisory_xact_lock(
                hashtext('lucky_draw')
            )
        ");


        // Tentukan sumber tabel kehadiran
        $tableSource = $isTetap ? 'ramah_tamah.v_kehadiran_tetap' : 'ramah_tamah.kehadiran';

        /*
         * Ambil N peserta sekaligus.
         *
         * Peserta harus:
         * - sudah hadir (dari sumber yang sesuai)
         * - belum pernah menjadi pemenang
         */
        $stmt = $pdo->prepare("
            SELECT
                p.id,
                p.nik,
                p.nama,
                p.departemen
            FROM ramah_tamah.peserta p

            INNER JOIN {$tableSource} k
                ON k.peserta_id = p.id

            LEFT JOIN ramah_tamah.pemenang w
                ON w.peserta_id = p.id

            WHERE w.id IS NULL

            ORDER BY RANDOM()

            LIMIT :jumlah_pemenang
        ");

        $stmt->bindValue(
            ':jumlah_pemenang',
            $jumlahPemenang,
            PDO::PARAM_INT
        );

        $stmt->execute();

        $pesertaList = $stmt->fetchAll();


        if (!$pesertaList) {

            $pdo->rollBack();

            return [
                'success' => false,
                'type' => 'no_participant',
                'message' => 'Tidak ada peserta yang dapat diundi.'
            ];
        }


        /*
         * Kalau peserta yang tersedia kurang
         * dari jumlah yang diminta, jangan diam-diam
         * mengurangi jumlah pemenang.
         */
        if (count($pesertaList) < $jumlahPemenang) {

            $tersedia = count($pesertaList);

            $pdo->rollBack();

            return [
                'success' => false,
                'type' => 'insufficient_participant',
                'message' =>
                "Peserta yang tersedia hanya {$tersedia} orang, " .
                    "sedangkan diminta {$jumlahPemenang} pemenang.",
                'available' => $tersedia,
                'requested' => $jumlahPemenang
            ];
        }


        // Cari / buat hadiah
        $hadiah = getOrCreatePrize(
            $pdo,
            $namaHadiah
        );


        /*
         * Insert seluruh pemenang.
         */
        $stmt = $pdo->prepare("
            INSERT INTO ramah_tamah.pemenang (
                peserta_id,
                hadiah_id,
                waktu_menang,
                status_pengambilan
            )
            VALUES (
                :peserta_id,
                :hadiah_id,
                NOW(),
                'MENANG'
            )
            RETURNING
                id,
                waktu_menang
        ");


        $winners = [];


        foreach ($pesertaList as $peserta) {

            $stmt->execute([
                ':peserta_id' => $peserta['id'],
                ':hadiah_id' => $hadiah['id']
            ]);

            $winner = $stmt->fetch();


            $winners[] = [
                'id' => $winner['id'],
                'peserta_id' => $peserta['id'],
                'nik' => $peserta['nik'],
                'nama' => $peserta['nama'],
                'departemen' => $peserta['departemen'],
                'hadiah' => $hadiah['nama_hadiah'],
                'waktu_menang' => $winner['waktu_menang']
            ];
        }


        $pdo->commit();


        return [
            'success' => true,
            'type' => 'success',
            'message' =>
            "{$jumlahPemenang} pemenang berhasil dipilih.",
            'winners' => $winners
        ];
    } catch (Throwable $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        throw $e;
    }
}


// ============================================================
// LUCKY DRAW - WINNER LIST
// ============================================================

/**
 * Ambil seluruh daftar pemenang.
 *
 * Digunakan oleh halaman Lucky Draw
 * untuk mengisi sidebar "Daftar Pemenang".
 */
function getWinnerList(): array
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

// ============================================================
// PRIZE PICKUP - CHECK
// ============================================================

/**
 * Cek apakah NIK merupakan pemenang Lucky Draw.
 */
function checkPrize(string $nik): array
{
    $nik = trim($nik);

    if ($nik === '') {
        return [
            'success' => false,
            'type' => 'validation',
            'message' => 'NIK belum diisi.'
        ];
    }

    $pdo = getDatabaseConnection();

    $stmt = $pdo->prepare("
        SELECT
            w.id,
            w.peserta_id,
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
        WHERE p.nik = :nik
        LIMIT 1
    ");

    $stmt->execute([
        ':nik' => $nik
    ]);

    $winner = $stmt->fetch();

    if (!$winner) {
        return [
            'success' => false,
            'type' => 'not_winner',
            'message' => 'Peserta bukan merupakan pemenang Lucky Draw.'
        ];
    }

    return [
        'success' => true,
        'type' => 'success',
        'message' => 'Data pemenang ditemukan.',
        'winner' => $winner
    ];
}

// ============================================================
// PRIZE PICKUP - TAKE PRIZE
// ============================================================

/**
 * Konfirmasi pengambilan hadiah.
 *
 * Menggunakan row lock agar dua scanner
 * tidak dapat mengonfirmasi hadiah yang sama
 * secara bersamaan.
 */
function takePrize(int $pemenangId): array
{
    if ($pemenangId < 1) {
        return [
            'success' => false,
            'type' => 'validation',
            'message' => 'ID pemenang tidak valid.'
        ];
    }

    $pdo = getDatabaseConnection();

    try {

        $pdo->beginTransaction();

        /*
         * Kunci baris pemenang.
         *
         * Jika scanner lain sedang memproses
         * pemenang yang sama, proses ini akan menunggu
         * sampai transaksi scanner pertama selesai.
         */
        $stmt = $pdo->prepare("
            SELECT
                w.id,
                w.status_pengambilan,
                w.waktu_pengambilan,
                p.nik,
                p.nama,
                p.departemen,
                h.nama_hadiah AS hadiah
            FROM ramah_tamah.pemenang w
            INNER JOIN ramah_tamah.peserta p
                ON p.id = w.peserta_id
            INNER JOIN ramah_tamah.hadiah h
                ON h.id = w.hadiah_id
            WHERE w.id = :id
            FOR UPDATE
        ");

        $stmt->execute([
            ':id' => $pemenangId
        ]);

        $winner = $stmt->fetch();

        /*
         * Pemenang tidak ditemukan.
         */
        if (!$winner) {

            $pdo->rollBack();

            return [
                'success' => false,
                'type' => 'not_found',
                'message' => 'Data pemenang tidak ditemukan.'
            ];
        }

        /*
         * Hadiah sudah pernah diambil.
         */
        if ($winner['status_pengambilan'] === 'DIAMBIL') {

            $pdo->rollBack();

            return [
                'success' => false,
                'type' => 'already_taken',
                'message' => 'Hadiah sudah diambil sebelumnya.',
                'winner' => $winner
            ];
        }

        /*
         * Ubah status menjadi DIAMBIL.
         */
        $stmt = $pdo->prepare("
            UPDATE ramah_tamah.pemenang
            SET
                status_pengambilan = 'DIAMBIL',
                waktu_pengambilan = NOW()
            WHERE id = :id
            RETURNING
                id,
                status_pengambilan,
                waktu_pengambilan
        ");

        $stmt->execute([
            ':id' => $pemenangId
        ]);

        $result = $stmt->fetch();

        $pdo->commit();

        return [
            'success' => true,
            'type' => 'success',
            'message' => 'Hadiah berhasil dikonfirmasi sebagai sudah diambil.',
            'winner' => [
                'id' => $winner['id'],
                'nik' => $winner['nik'],
                'nama' => $winner['nama'],
                'departemen' => $winner['departemen'],
                'hadiah' => $winner['hadiah'],
                'status_pengambilan' => $result['status_pengambilan'],
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
