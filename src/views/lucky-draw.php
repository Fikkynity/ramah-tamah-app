<div class="container-fluid">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="mb-1">
                Lucky Draw
            </h2>

            <p class="text-muted mb-0">
                Pengundian hadiah untuk peserta yang telah hadir.
            </p>
        </div>

    </div>


    <div class="row g-4">

        <!-- ========================================
             LEFT - DRAW CONTROL
        ======================================== -->

        <div class="col-lg-8">

            <div class="card shadow-sm">

                <div class="card-body p-4">

                    <!-- MODE TOGGLE -->
                    <div class="d-flex justify-content-between align-items-center mb-4">

                        <div>
                            <h5 class="mb-1">
                                Mode Pengundian
                            </h5>

                            <small class="text-muted">
                                Pilih metode pengundian.
                            </small>
                        </div>


                        <div class="btn-group" role="group">

                            <input
                                type="radio"
                                class="btn-check"
                                name="drawMode"
                                id="modeAuto"
                                value="auto"
                                checked
                                autocomplete="off">

                            <label
                                class="btn btn-outline-primary"
                                for="modeAuto">
                                Auto
                            </label>


                            <input
                                type="radio"
                                class="btn-check"
                                name="drawMode"
                                id="modeManual"
                                value="manual"
                                autocomplete="off">

                            <label
                                class="btn btn-outline-primary"
                                for="modeManual">
                                Manual
                            </label>

                        </div>

                    </div>


                    <!-- ========================================
                         AUTO MODE
                    ======================================== -->

                    <div id="autoMode">

                        <div class="row g-3">

                            <!-- HADIAH -->
                            <div class="col-md-4">

                                <label
                                    for="autoPrize"
                                    class="form-label fw-semibold">
                                    Hadiah
                                </label>

                                <input
                                    type="text"
                                    id="autoPrize"
                                    class="form-control form-control-lg"
                                    placeholder="Masukkan Hadiah">

                            </div>


                            <!-- JUMLAH PEMENANG -->
                            <div class="col-md-4">

                                <label
                                    for="winnerCount"
                                    class="form-label fw-semibold">
                                    Banyak Pemenang
                                </label>

                                <input
                                    type="number"
                                    id="winnerCount"
                                    class="form-control form-control-lg"
                                    min="1"
                                    value="1">

                            </div>


                            <!-- DURASI ANIMASI -->
                            <div class="col-md-4">

                                <label
                                    for="drawInterval"
                                    class="form-label fw-semibold">
                                    Durasi Animasi (detik)
                                </label>

                                <input
                                    type="number"
                                    id="drawInterval"
                                    class="form-control form-control-lg"
                                    min="1"
                                    value="5">

                            </div>

                        </div>


                        <div class="d-flex justify-content-end mt-4">

                            <button
                                type="button"
                                id="autoDrawButton"
                                class="btn btn-primary btn-lg px-5">

                                Mulai Draw

                            </button>

                        </div>

                    </div>


                    <!-- ========================================
                         MANUAL MODE
                    ======================================== -->

                    <div
                        id="manualMode"
                        class="d-none">

                        <div class="row align-items-end g-3">

                            <!-- HADIAH -->
                            <div class="col-md-8">

                                <label
                                    for="manualPrize"
                                    class="form-label fw-semibold">
                                    Hadiah
                                </label>

                                <input
                                    type="text"
                                    id="manualPrize"
                                    class="form-control form-control-lg"
                                    placeholder="Masukkan Hadiah">

                            </div>


                            <!-- BUTTON DRAW -->
                            <div class="col-md-4">

                                <button
                                    type="button"
                                    id="manualDrawButton"
                                    class="btn btn-primary btn-lg w-100">

                                    DRAW

                                </button>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- ========================================
                 CURRENT WINNER
            ======================================== -->

            <div class="card shadow-sm mt-4">

                <div class="card-body p-5 text-center">

                    <small class="text-muted">
                        PEMENANG TERBARU
                    </small>

                    <div
                        id="currentWinner"
                        class="winner-display mt-3">

                        <div class="winner-placeholder">

                            <div class="winner-icon">
                                ?
                            </div>

                            <h4 class="mt-3 mb-1">
                                Menunggu Pengundian
                            </h4>

                            <p class="text-muted mb-0">
                                Pemenang akan muncul di sini.
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- ========================================
             RIGHT - WINNER LIST
        ======================================== -->

        <div class="col-lg-4">

            <div class="card shadow-sm h-100">

                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-center mb-3">

                        <div>
                            <h5 class="mb-1">
                                Daftar Pemenang
                            </h5>

                            <small class="text-muted">
                                Pemenang yang sudah terpilih.
                            </small>
                        </div>

                        <span
                            id="winnerCountBadge"
                            class="badge bg-warning text-dark">
                            0
                        </span>

                    </div>


                    <div
                        id="winnerList"
                        class="winner-list">

                        <div
                            class="text-center text-muted py-5">

                            Belum ada pemenang.

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>