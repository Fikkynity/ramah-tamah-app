<div class="dashboard-page">

    <!-- ================================================== -->
    <!-- AUTO SCROLL TOGGLE (FIXED - POJOK KANAN ATAS) -->
    <!-- ================================================== -->

    <div class="autoscroll-toggle-wrapper shadow-sm">

        <div class="form-check form-switch mb-0">

            <input
                class="form-check-input"
                type="checkbox"
                role="switch"
                id="autoScrollToggle"
                checked>

            <label
                class="form-check-label fw-semibold"
                for="autoScrollToggle">
                Auto Scroll
            </label>

        </div>

    </div>


    <!-- ================================================== -->
    <!-- PAGE HEADER -->
    <!-- ================================================== -->

    <div class="mb-4">

        <h2 class="mb-1">
            Dashboard Pemenang
        </h2>

        <p class="text-muted mb-0">
            Daftar pemenang Lucky Draw beserta status pengambilan hadiah.
        </p>

    </div>


    <!-- ================================================== -->
    <!-- WINNER GROUPS (DIISI OLEH JAVASCRIPT) -->
    <!-- ================================================== -->

    <div id="dashboardWinnerGroups">

        <div class="text-center text-muted py-5">
            Memuat data pemenang...
        </div>

    </div>

</div>


<style>

    .autoscroll-toggle-wrapper {
        position: fixed;
        top: 16px;
        right: 24px;
        z-index: 1050;
        background-color: #ffffff;
        padding: 10px 18px;
        border-radius: 999px;
        border: 1px solid rgba(0, 0, 0, 0.08);
    }

    .dashboard-winner-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .dashboard-winner-row {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 16px;
        padding: 12px 18px;
        border-radius: 8px;
        font-weight: 600;
        text-align: center;
        transition: transform 0.1s ease;
    }

    .dashboard-winner-row:hover {
        transform: scale(1.01);
    }

    .dashboard-winner-number {
        font-weight: 700;
        opacity: 0.75;
    }

    .dashboard-winner-nik {
        font-variant-numeric: tabular-nums;
    }

    .dashboard-winner-name {
        font-weight: 700;
    }

    .dashboard-winner-dept {
        font-weight: 500;
        opacity: 0.85;
    }

    /* BELUM DIAMBIL */
    .dashboard-winner-kuning {
        background-color: #ffc107;
        color: #212529;
    }

    /* SUDAH DIAMBIL */
    .dashboard-winner-hijau {
        background-color: #198754;
        color: #ffffff;
    }

    @media (max-width: 576px) {

        .dashboard-winner-row {
            flex-wrap: wrap;
        }

    }

</style>
