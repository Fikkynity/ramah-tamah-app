<div class="container-fluid">

    <!-- ================================================== -->
    <!-- PAGE HEADER -->
    <!-- ================================================== -->

    <div class="mb-4">

        <h2 class="mb-1">
            Scan Pengambilan Hadiah
        </h2>

        <p class="text-muted mb-0">
            Verifikasi pemenang sebelum hadiah diberikan.
        </p>

    </div>


    <!-- ================================================== -->
    <!-- VERIFICATION -->
    <!-- ================================================== -->

    <div class="row g-4">


        <!-- ================================================== -->
        <!-- SCANNER -->
        <!-- ================================================== -->

        <div class="col-lg-5">

            <div class="card shadow-sm h-100">

                <div class="card-body p-4">

                    <h5 class="mb-1">
                        Verifikasi Pemenang
                    </h5>

                    <p class="text-muted mb-4">
                        Scan QR Code peserta atau masukkan NIK secara manual.
                    </p>


                    <!-- QR SCANNER -->

                    <div
                        id="qrScanner"
                        class="border rounded-3 d-flex align-items-center justify-content-center text-center mb-4"
                        style="
                            height: 280px;
                            background-color: #f8f9fa;
                        ">

                        <div>

                            <div
                                class="mb-3 text-muted"
                                style="font-size: 48px;">
                                ▣
                            </div>

                            <h6 class="mb-1">
                                QR Scanner
                            </h6>

                            <p class="text-muted small mb-0">
                                Kamera scanner akan digunakan di sini.
                            </p>

                        </div>

                    </div>


                    <!-- DIVIDER -->

                    <div class="d-flex align-items-center gap-3 mb-4">

                        <div class="flex-grow-1 border-top"></div>

                        <span class="text-muted small">
                            ATAU
                        </span>

                        <div class="flex-grow-1 border-top"></div>

                    </div>


                    <!-- MANUAL NIK -->

                    <form id="prizeCheckForm">

                        <label
                            for="prizeNikInput"
                            class="form-label fw-semibold">
                            NIK Peserta
                        </label>

                        <div class="input-group input-group-lg">

                            <input
                                type="text"
                                id="prizeNikInput"
                                class="form-control"
                                placeholder="Masukkan NIK"
                                autocomplete="off">

                            <button
                                type="submit"
                                id="prizeCheckButton"
                                class="btn btn-primary px-4">
                                Cek
                            </button>

                        </div>

                        <div class="form-text">
                            Masukkan NIK peserta yang akan mengambil hadiah.
                        </div>

                    </form>

                </div>

            </div>

        </div>


        <!-- ================================================== -->
        <!-- RESULT -->
        <!-- ================================================== -->

        <div class="col-lg-7">

            <div class="card shadow-sm h-100">

                <div class="card-body p-4">

                    <h5 class="mb-1">
                        Hasil Verifikasi
                    </h5>

                    <p class="text-muted mb-4">
                        Informasi pemenang dan status hadiah.
                    </p>


                    <!-- ================================================== -->
                    <!-- EMPTY -->
                    <!-- ================================================== -->

                    <div
                        id="prizeEmpty"
                        class="text-center py-5">

                        <div
                            class="text-muted mb-3"
                            style="font-size: 56px;">
                            ?
                        </div>

                        <h5>
                            Menunggu Scan
                        </h5>

                        <p class="text-muted mb-0">
                            Scan QR Code atau masukkan NIK peserta.
                        </p>

                    </div>


                    <!-- ================================================== -->
                    <!-- RESULT -->
                    <!-- ================================================== -->

                    <div
                        id="prizeResult"
                        class="d-none">

                        <div
                            id="prizeStatusCard"
                            class="border rounded-3 p-4">

                            <!-- STATUS -->

                            <div
                                id="prizeStatus"
                                class="mb-4">
                            </div>


                            <!-- PARTICIPANT -->

                            <div class="mb-4">

                                <small class="text-muted">
                                    NAMA PESERTA
                                </small>

                                <h3
                                    id="prizeParticipantName"
                                    class="mb-1">
                                    -
                                </h3>

                                <div
                                    id="prizeParticipantDepartment"
                                    class="text-muted">
                                    -
                                </div>

                            </div>


                            <!-- NIK -->

                            <div class="mb-3">

                                <small class="text-muted">
                                    NIK
                                </small>

                                <div
                                    id="prizeParticipantNik"
                                    class="fw-semibold">
                                    -
                                </div>

                            </div>


                            <!-- HADIAH -->

                            <div class="mb-4">

                                <small class="text-muted">
                                    HADIAH
                                </small>

                                <h4
                                    id="prizeName"
                                    class="mb-0">
                                    -
                                </h4>

                            </div>


                            <!-- ACTION -->

                            <div id="prizeAction">

                                <button
                                    type="button"
                                    id="takePrizeButton"
                                    class="btn btn-success btn-lg w-100">
                                    Konfirmasi Pengambilan Hadiah
                                </button>

                            </div>

                        </div>

                    </div>


                    <!-- ================================================== -->
                    <!-- ERROR -->
                    <!-- ================================================== -->

                    <div
                        id="prizeError"
                        class="d-none">

                        <div class="text-center py-5">

                            <div
                                class="text-danger mb-3"
                                style="font-size: 56px;">
                                ✕
                            </div>

                            <h5 id="prizeErrorTitle">
                                Peserta Tidak Ditemukan
                            </h5>

                            <p
                                id="prizeErrorMessage"
                                class="text-muted mb-0">
                                Data peserta tidak ditemukan.
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- ================================================== -->
    <!-- WINNER / PICKUP LIST -->
    <!-- ================================================== -->

    <div class="card shadow-sm mt-4">

        <div class="card-body p-4">

            <!-- HEADER -->

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h5 class="mb-1">
                        Daftar Pemenang & Pengambilan Hadiah
                    </h5>

                    <small class="text-muted">
                        Daftar seluruh pemenang dan status pengambilan hadiah.
                    </small>

                </div>

                <span
                    id="prizeWinnerCount"
                    class="badge bg-primary">
                    0 Pemenang
                </span>

            </div>


            <!-- TABLE -->

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>

                            <th>
                                No
                            </th>

                            <th>
                                NIK
                            </th>

                            <th>
                                Nama
                            </th>

                            <th>
                                Departemen
                            </th>

                            <th>
                                Hadiah
                            </th>

                            <th>
                                Waktu Menang
                            </th>

                            <th>
                                Waktu Pengambilan
                            </th>

                            <th>
                                Status
                            </th>

                        </tr>

                    </thead>

                    <tbody id="prizeWinnerTable">

                        <tr>

                            <td
                                colspan="8"
                                class="text-center text-muted py-4">
                                Belum ada pemenang.

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>