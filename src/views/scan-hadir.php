<div class="container-fluid">

    <!-- PAGE HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Scan Daftar Hadir</h2>
            <p class="text-muted mb-0">
                Scan QR peserta atau masukkan NIK secara manual.
            </p>
        </div>
    </div>


    <div class="row g-4">

        <!-- ========================================
             SCANNER / INPUT
        ========================================= -->
        <div class="col-lg-5">

            <div class="card shadow-sm h-100">

                <div class="card-body p-4">

                    <h5 class="card-title mb-4">
                        Verifikasi Peserta
                    </h5>


                    <!-- QR SCANNER AREA -->
                    <div class="scan-box mb-4">

                        <div class="scan-icon">
                            <span>▣</span>
                        </div>

                        <h5 class="mt-3 mb-2">
                            Scan QR Code
                        </h5>

                        <p class="text-muted small mb-0">
                            Arahkan QR pada tiket peserta ke scanner.
                        </p>

                    </div>


                    <!-- MANUAL INPUT -->
                    <div class="text-center text-muted mb-3">
                        atau masukkan NIK secara manual
                    </div>

                    <form id="attendanceForm">

                        <div class="mb-3">

                            <label
                                for="nikInput"
                                class="form-label fw-semibold">
                                NIK
                            </label>

                            <input
                                type="text"
                                class="form-control form-control-lg"
                                id="nikInput"
                                name="nik"
                                placeholder="Masukkan NIK peserta"
                                autocomplete="off"
                                autofocus>

                        </div>

                        <button
                            type="submit"
                            class="btn btn-primary btn-lg w-100">

                            Verifikasi Peserta

                        </button>

                    </form>

                </div>

            </div>

        </div>


        <!-- ========================================
             HASIL SCAN
        ========================================= -->
        <div class="col-lg-7">

            <div class="card shadow-sm h-100">

                <div class="card-body p-4">

                    <h5 class="card-title mb-4">
                        Hasil Verifikasi
                    </h5>


                    <!-- DEFAULT STATE -->
                    <div
                        id="scanResult"
                        class="scan-result text-center">

                        <div class="result-icon">
                            —
                        </div>

                        <h5 class="mt-3">
                            Menunggu Scan
                        </h5>

                        <p class="text-muted mb-0">
                            Silakan scan QR atau masukkan NIK peserta.
                        </p>

                    </div>


                    <!-- PESERTA -->
                    <div
                        id="participantResult"
                        class="d-none">

                        <div class="border rounded p-4">

                            <div class="d-flex justify-content-between align-items-start mb-4">

                                <div>
                                    <small class="text-muted">
                                        Peserta
                                    </small>

                                    <h4
                                        id="participantName"
                                        class="mb-1">
                                        -
                                    </h4>

                                    <span
                                        id="participantNik"
                                        class="text-muted">
                                        -
                                    </span>
                                </div>

                                <span
                                    id="attendanceStatus"
                                    class="badge bg-success">
                                    HADIR
                                </span>

                            </div>


                            <div class="row g-3">

                                <div class="col-md-6">

                                    <div class="bg-light rounded p-3">

                                        <small class="text-muted">
                                            Departemen
                                        </small>

                                        <div
                                            id="participantDepartment"
                                            class="fw-semibold mt-1">
                                            -
                                        </div>

                                    </div>

                                </div>


                                <div class="col-md-6">

                                    <div class="bg-light rounded p-3">

                                        <small class="text-muted">
                                            Waktu Datang
                                        </small>

                                        <div
                                            id="arrivalTime"
                                            class="fw-semibold mt-1">
                                            -
                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- ERROR -->
                    <div
                        id="scanError"
                        class="alert alert-danger d-none mt-4 mb-0">

                        <strong>Peserta tidak ditemukan.</strong>

                        <div class="small mt-1">
                            Silakan periksa kembali QR atau NIK yang dimasukkan.
                        </div>

                    </div>


                    <!-- ALREADY ATTENDED -->
                    <div
                        id="alreadyAttended"
                        class="alert alert-warning d-none mt-4 mb-0">

                        <strong>Peserta sudah melakukan scan.</strong>

                        <div class="small mt-1">
                            Peserta ini sudah tercatat sebagai hadir.

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- ========================================
         RECENT ATTENDANCE
    ========================================= -->
    <div class="card shadow-sm mt-4">

        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-center mb-3">

                <div>
                    <h5 class="mb-1">
                        Daftar Kehadiran
                    </h5>

                    <small class="text-muted">
                        Peserta yang sudah melakukan scan masuk.
                    </small>
                </div>

                <span
                    class="badge bg-primary"
                    id="attendanceCount">
                    0 Peserta
                </span>

            </div>


            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>
                            <th>No</th>
                            <th>NIK</th>
                            <th>Nama</th>
                            <th>Departemen</th>
                            <th>Waktu Datang</th>
                            <th>Status</th>
                        </tr>

                    </thead>

                    <tbody id="attendanceTable">

                        <tr>
                            <td
                                colspan="6"
                                class="text-center text-muted py-4">

                                Belum ada peserta yang melakukan scan.

                            </td>
                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>