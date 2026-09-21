<?php

// ========================================
// CONNECTION / CONTROLLER
// ========================================

require_once dirname(__DIR__) . '/src/controllers/AttendanceController.php';
require_once dirname(__DIR__) . '/src/controllers/AuthController.php';
require_once dirname(__DIR__) . '/src/controllers/ScanController.php';
require_once dirname(__DIR__) . '/src/controllers/UserManagementController.php';


// ========================================
// API - LOGIN
// ========================================

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && ($_GET['action'] ?? '') === 'login'
) {

    header('Content-Type: application/json; charset=utf-8');

    try {

        $input = json_decode(
            file_get_contents('php://input'),
            true
        );

        $nik = trim($input['nik'] ?? '');
        $password = trim($input['password'] ?? '');

        echo json_encode(
            login($nik, $password),
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
// API - USER DASHBOARD
// ========================================

if (
    $_SERVER['REQUEST_METHOD'] === 'GET'
    && ($_GET['action'] ?? '') === 'user-dashboard'
) {

    header('Content-Type: application/json; charset=utf-8');

    try {

        $nik = trim($_GET['nik'] ?? '');

        echo json_encode(
            getUserDashboard($nik),
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
// API - ADMIN STATS
// ========================================

if (
    $_SERVER['REQUEST_METHOD'] === 'GET'
    && ($_GET['action'] ?? '') === 'admin-stats'
) {

    header('Content-Type: application/json; charset=utf-8');

    try {

        echo json_encode(
            getAdminStats(),
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
// API - SCAN ATTENDANCE (ADMIN)
// ========================================

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && ($_GET['action'] ?? '') === 'scan-attendance'
) {

    header('Content-Type: application/json; charset=utf-8');

    try {

        $input = json_decode(
            file_get_contents('php://input'),
            true
        );

        $nik = trim($input['nik'] ?? '');

        echo json_encode(
            scanAttendance($nik),
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
// API - SCAN PRIZE PICKUP (ADMIN)
// ========================================

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && ($_GET['action'] ?? '') === 'scan-prize'
) {

    header('Content-Type: application/json; charset=utf-8');

    try {

        $input = json_decode(
            file_get_contents('php://input'),
            true
        );

        $nik = trim($input['nik'] ?? '');

        echo json_encode(
            scanPrizePickup($nik),
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
// API - WINNERS LIST WITH PAGINATION
// ========================================

if (
    $_SERVER['REQUEST_METHOD'] === 'GET'
    && ($_GET['action'] ?? '') === 'winners-paginated'
) {

    header('Content-Type: application/json; charset=utf-8');

    try {

        $page = (int)($_GET['page'] ?? 1);
        $perPage = (int)($_GET['per_page'] ?? 50);

        echo json_encode(
            getWinnersList($page, $perPage),
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
// API - USERS LIST (ADMIN)
// ========================================

if (
    $_SERVER['REQUEST_METHOD'] === 'GET'
    && ($_GET['action'] ?? '') === 'users-list'
) {

    header('Content-Type: application/json; charset=utf-8');

    try {

        $search = trim($_GET['search'] ?? '');
        $role = trim($_GET['role'] ?? '');
        $page = (int)($_GET['page'] ?? 1);
        $perPage = (int)($_GET['per_page'] ?? 50);

        echo json_encode(
            getUsersList($search, $role, $page, $perPage),
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
// API - UPDATE USER ROLE (ADMIN)
// ========================================

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && ($_GET['action'] ?? '') === 'update-user-role'
) {

    header('Content-Type: application/json; charset=utf-8');

    try {

        $input = json_decode(
            file_get_contents('php://input'),
            true
        );

        $nik = trim($input['nik'] ?? '');
        $role = trim($input['role'] ?? '');

        echo json_encode(
            updateUserRole($nik, $role),
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

        // Mode khusus: m=1 berarti pool karyawan tetap
        $isTetap = (($_GET['m'] ?? '') === '1');

        $data = getAttendanceList($isTetap);

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

        // Mode khusus: m=1 berarti pool karyawan tetap
        $isTetap = (trim($input['m'] ?? '') === '1');

        echo json_encode(
            drawWinner($hadiah, $isTetap),
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

        // Mode khusus: m=1 berarti pool karyawan tetap
        $isTetap = (trim($input['m'] ?? '') === '1');

        echo json_encode(
            drawWinnerBatch($hadiah, $jumlah, $isTetap),
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
// API - CHECK PRIZE
// ========================================

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && ($_GET['action'] ?? '') === 'check-prize'
) {

    header('Content-Type: application/json; charset=utf-8');

    try {

        $input = json_decode(
            file_get_contents('php://input'),
            true
        );

        $nik = trim($input['nik'] ?? '');

        echo json_encode(
            checkPrize($nik),
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
// API - TAKE PRIZE
// ========================================

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && ($_GET['action'] ?? '') === 'take-prize'
) {

    header('Content-Type: application/json; charset=utf-8');

    try {

        $input = json_decode(
            file_get_contents('php://input'),
            true
        );

        $pemenangId = (int) ($input['pemenang_id'] ?? 0);

        echo json_encode(
            takePrize($pemenangId),
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
