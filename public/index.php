<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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

$pageTitle = match ($page) {
    'dashboard' => 'Dashboard',
    'scan-hadir' => 'Scan Daftar Hadir',
    'lucky-draw' => 'Lucky Draw',
    'scan-hadiah' => 'Scan Pengambilan Hadiah',
};

$currentPage = $page;

require __DIR__ . '/../src/views/layouts/header.php';

require __DIR__ . '/../src/views/layouts/sidebar.php';
?>

<main class="content flex-grow-1 p-4">

    <?php require __DIR__ . '/../src/views/' . $page . '.php'; ?>

</main>

<?php

require __DIR__ . '/../src/views/layouts/footer.php';
