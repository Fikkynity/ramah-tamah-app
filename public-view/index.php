<?php

// ========================================
// APLIKASI PUBLIK - DAFTAR PEMENANG
//
// File ini BERDIRI SENDIRI, terpisah total
// dari aplikasi admin (public/index.php).
// Tidak ada require ke src/controllers/
// AttendanceController.php sama sekali,
// jadi fungsi draw-winner, take-prize, dll
// TIDAK ADA di server ini - bukan cuma
// tidak dipanggil, memang tidak ada
// kodenya di sini.
//
// Jalankan folder ini di proses/port yang
// TERPISAH dari aplikasi admin, misalnya:
//   php -S 0.0.0.0:8081 -t public-view
// lalu HANYA port itu yang di-tunnel ke
// internet.
// ========================================

require_once __DIR__ . '/controllers/PublicWinnerController.php';


// ========================================
// API - WINNER LIST (READ ONLY, GET SAJA)
// ========================================

if (
    $_SERVER['REQUEST_METHOD'] === 'GET'
    && ($_GET['action'] ?? '') === 'winner-list'
) {

    header('Content-Type: application/json; charset=utf-8');

    try {

        $data = getPublicWinnerList();

        echo json_encode([
            'success' => true,
            'data' => $data
        ], JSON_UNESCAPED_UNICODE);
    } catch (Throwable $e) {

        http_response_code(500);

        echo json_encode([
            'success' => false,
            'message' => 'Gagal mengambil data pemenang.'
        ], JSON_UNESCAPED_UNICODE);
    }

    exit;
}


// ========================================
// HALAMAN
// ========================================

$pageTitle = 'Daftar Pemenang - Ramah Tamah 2026';

?>
<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($pageTitle) ?></title>

    <link
        rel="stylesheet"
        href="assets/css/bootstrap.min.css">

    <link
        rel="stylesheet"
        href="assets/css/style.css">

</head>

<body>

    <main class="content p-4">

        <?php require __DIR__ . '/dashboard-user.php'; ?>

    </main>

    <script src="assets/js/dashboard-user.js"></script>

</body>

</html>
