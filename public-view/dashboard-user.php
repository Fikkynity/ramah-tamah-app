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
    <!-- SEARCH BOX (CENTER) -->
    <!-- ================================================== -->

    <div class="d-flex justify-content-center mb-4">

        <div class="dashboard-search-box">

            <label
                for="dashboardWinnerSearch"
                class="form-label small text-muted mb-1 text-center d-block">
                Cari NIK / Nama Peserta
            </label>

            <div class="input-group">

                <span class="input-group-text bg-white">
                    🔍
                </span>

                <input
                    type="text"
                    id="dashboardWinnerSearch"
                    class="form-control"
                    placeholder="Masukkan NIK atau nama..."
                    autocomplete="off">

                <button
                    type="button"
                    id="dashboardWinnerSearchClear"
                    class="btn btn-outline-secondary d-none"
                    title="Hapus pencarian">
                    ✕
                </button>

            </div>

            <div
                id="dashboardSearchResultInfo"
                class="form-text text-center">
            </div>

        </div>

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

    .dashboard-search-box {
        min-width: 280px;
        max-width: 420px;
        width: 100%;
    }

    .dashboard-winner-row mark.dashboard-search-highlight,
    .dashboard-winner-item mark.dashboard-search-highlight {
        background-color: rgba(255, 255, 255, 0.9);
        color: #000000;
        padding: 0 3px;
        border-radius: 3px;
    }

    /*
     * Grid, bukan 1 nama ke bawah.

     * auto-fill + minmax bikin jumlah kolom
     * menyesuaikan lebar layar secara
     * otomatis (di laptop bisa ~4-5 kolom,
     * di TV/proyektor lebar bisa ~7-8 kolom)
     * - jadi tetap enak dibaca di device
     * manapun tanpa perlu angka kolom yang
     * di-hardcode.
     *
     * Efeknya juga bikin total tinggi
     * halaman jauh lebih pendek walau
     * pemenangnya ratusan, sehingga siklus
     * auto-scroll jadi lebih singkat dan
     * tiap nama lebih gampang ke-notice.
     */
    .dashboard-winner-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));

        gap: 10px;
    }

    .dashboard-winner-item {
        padding: 12px 10px;
        border-radius: 10px;
        font-weight: 600;
        text-align: center;
        transition: transform 0.1s ease;
    }

    .dashboard-winner-item:hover {
        transform: scale(1.03);
    }

    .dashboard-winner-number {
        font-size: 12px;
        font-weight: 700;
        opacity: 0.7;

        margin-bottom: 4px;
    }

    .dashboard-winner-name {
        font-weight: 700;

        margin-bottom: 2px;
    }

    .dashboard-winner-dept {
        font-size: 13px;
        font-weight: 500;
        opacity: 0.85;

        margin-bottom: 4px;
    }

    .dashboard-winner-nik {
        font-size: 12px;
        font-variant-numeric: tabular-nums;
        opacity: 0.8;
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

</style>
