<?php

// ========================================
// CONNECTION / CONTROLLER
// ========================================

require_once dirname(__DIR__) . '/src/controllers/AttendanceController.php';


// ========================================
// API - ATTENDANCE
// ========================================

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && ($_GET['action'] ?? '') === 'attendance'
) {

    header('Content-Type: application/json; charset=utf-8');

    try {

        $input = json_decode(
            file_get_contents('php://input'),
            true
        );

        $nik = trim($input['nik'] ?? '');

        echo json_encode(
            verifyAttendance($nik),
            JSON_UNESCAPED_UNICODE
        );
    } catch (Throwable $e) {

        http_response_code(500);

        echo json_encode([
            'success' => false,
            'type' => 'server_error',
            'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }

    exit;
}


// ========================================
// API - ATTENDANCE LIST
// ========================================

if (
    $_SERVER['REQUEST_METHOD'] === 'GET'
    && ($_GET['action'] ?? '') === 'attendance-list'
) {

    header('Content-Type: application/json; charset=utf-8');

    try {

        $data = getAttendanceList();

        echo json_encode([
            'success' => true,
            'data' => $data
        ], JSON_UNESCAPED_UNICODE);
    } catch (Throwable $e) {

        http_response_code(500);

        echo json_encode([
            'success' => false,
            'type' => 'server_error',
            'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }

    exit;
}


// ========================================
// API - DRAW WINNER - MANUAL
// ========================================

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && ($_GET['action'] ?? '') === 'draw-winner'
) {

    header('Content-Type: application/json; charset=utf-8');

    try {

        $input = json_decode(
            file_get_contents('php://input'),
            true
        );

        $hadiah = trim($input['hadiah'] ?? '');

        echo json_encode(
            drawWinner($hadiah),
            JSON_UNESCAPED_UNICODE
        );
    } catch (Throwable $e) {

        http_response_code(500);

        echo json_encode([
            'success' => false,
            'type' => 'server_error',
            'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }

    exit;
}


// ========================================
// API - DRAW WINNER BATCH - AUTO
// ========================================

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && ($_GET['action'] ?? '') === 'draw-winner-batch'
) {

    header('Content-Type: application/json; charset=utf-8');

    try {

        $input = json_decode(
            file_get_contents('php://input'),
            true
        );

        $hadiah = trim($input['hadiah'] ?? '');

        $jumlah = (int) ($input['jumlah'] ?? 0);

        echo json_encode(
            drawWinnerBatch($hadiah, $jumlah),
            JSON_UNESCAPED_UNICODE
        );
    } catch (Throwable $e) {

        http_response_code(500);

        echo json_encode([
            'success' => false,
            'type' => 'server_error',
            'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }

    exit;
}


// ========================================
// API - WINNER LIST
// ========================================

if (
    $_SERVER['REQUEST_METHOD'] === 'GET'
    && ($_GET['action'] ?? '') === 'winner-list'
) {

    header('Content-Type: application/json; charset=utf-8');

    try {

        $data = getWinnerList();

        echo json_encode([
            'success' => true,
            'data' => $data
        ], JSON_UNESCAPED_UNICODE);
    } catch (Throwable $e) {

        http_response_code(500);

        echo json_encode([
            'success' => false,
            'type' => 'server_error',
            'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }

    exit;
}


// ========================================
// ERROR REPORTING
// ========================================

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// ========================================
// PAGE ROUTING
// ========================================

$page = $_GET['page'] ?? 'dashboard';

$allowedPages = [
    'dashboard',
    'scan-hadir',
    'lucky-draw',
    'scan-hadiah'
];

if (!in_array($page, $allowedPages, true)) {
    $page = 'dashboard';
}


// ========================================
// PAGE TITLE
// ========================================

$pageTitle = match ($page) {

    'dashboard' => 'Dashboard',

    'scan-hadir' => 'Scan Daftar Hadir',

    'lucky-draw' => 'Lucky Draw',

    'scan-hadiah' => 'Scan Pengambilan Hadiah',
};


// ========================================
// CURRENT PAGE
// ========================================

$currentPage = $page;


// ========================================
// HEADER
// ========================================

require __DIR__ . '/../src/views/layouts/header.php';


// ========================================
// SIDEBAR
// ========================================

require __DIR__ . '/../src/views/layouts/sidebar.php';

?>

<main class="content flex-grow-1 p-4">

    <?php

    require __DIR__ . '/../src/views/' . $page . '.php';

    ?>

</main>

<?php

// ========================================
// FOOTER
// ========================================

require __DIR__ . '/../src/views/layouts/footer.php';
