// ============================================================
// RAMAH TAMAH APP
// Main JavaScript
// ============================================================

// ============================================================
// GLOBAL STATE
// ============================================================

let attendancePool = [];

let winnerParticipantIds = new Set();

// ============================================================
// GLOBAL HELPERS
// ============================================================

function escapeHtml(value) {
  if (value === null || value === undefined) {
    return "";
  }

  return String(value)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

function formatDateTime(dateString) {
  if (!dateString) {
    return "-";
  }

  const date = new Date(dateString);

  if (isNaN(date.getTime())) {
    return dateString;
  }

  return date.toLocaleString("id-ID", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit",
    second: "2-digit",
  });
}

// ============================================================
// SIDEBAR
// ============================================================

function initSidebar() {
  const sidebar = document.getElementById("sidebar");
  const sidebarToggle = document.getElementById("sidebarToggle");
  const sidebarToggleIcon = document.getElementById("sidebarToggleIcon");

  if (!sidebar || !sidebarToggle) {
    return;
  }

  sidebarToggle.addEventListener("click", function () {
    sidebar.classList.toggle("collapsed");

    const isCollapsed = sidebar.classList.contains("collapsed");

    if (sidebarToggleIcon) {
      sidebarToggleIcon.classList.toggle("bi-chevron-left", !isCollapsed);

      sidebarToggleIcon.classList.toggle("bi-chevron-right", isCollapsed);
    }

    sidebarToggle.setAttribute(
      "aria-label",
      isCollapsed ? "Tampilkan sidebar" : "Sembunyikan sidebar",
    );
  });
}

// ============================================================
// ATTENDANCE - SCAN / MANUAL NIK
// ============================================================

function initAttendance() {
  const form = document.getElementById("attendanceForm");
  const nikInput = document.getElementById("nikInput");

  if (!form || !nikInput) {
    return;
  }

  form.addEventListener("submit", async function (event) {
    event.preventDefault();

    const nik = nikInput.value.trim();

    if (nik === "") {
      nikInput.focus();
      return;
    }

    try {
      const response = await fetch("index.php?action=attendance", {
        method: "POST",

        headers: {
          "Content-Type": "application/json",
        },

        body: JSON.stringify({
          nik: nik,
        }),
      });

      if (!response.ok) {
        throw new Error(`HTTP ${response.status}`);
      }

      const result = await response.json();

      console.log("Attendance response:", result);

      // ====================================================
      // BERHASIL ABSEN
      // ====================================================

      if (result.success) {
        showAttendanceSuccess({
          ...result.participant,
          waktu_datang: result.waktu_datang,
        });

        loadAttendanceList();

        return;
      }

      // ====================================================
      // SUDAH ABSEN
      // ====================================================

      if (result.type === "already_attended") {
        showAlreadyAttended({
          ...result.participant,
          waktu_datang: result.waktu_datang,
        });

        return;
      }

      // ====================================================
      // TIDAK DITEMUKAN
      // ====================================================

      if (result.type === "not_found") {
        showAttendanceError();

        return;
      }

      alert(result.message || "Terjadi kesalahan.");
    } catch (error) {
      console.error("Attendance error:", error);

      alert("Tidak dapat memproses permintaan ke server.");
    }
  });
}

// ============================================================
// ATTENDANCE - SUCCESS
// ============================================================

function showAttendanceSuccess(data) {
  const scanResult = document.getElementById("scanResult");

  const participantResult = document.getElementById("participantResult");

  const scanError = document.getElementById("scanError");

  const alreadyAttended = document.getElementById("alreadyAttended");

  if (!scanResult || !participantResult || !scanError || !alreadyAttended) {
    return;
  }

  scanResult.classList.add("d-none");

  scanError.classList.add("d-none");

  alreadyAttended.classList.add("d-none");

  participantResult.classList.remove("d-none");

  const participantName = document.getElementById("participantName");

  const participantNik = document.getElementById("participantNik");

  const participantDepartment = document.getElementById(
    "participantDepartment",
  );

  const arrivalTime = document.getElementById("arrivalTime");

  const status = document.getElementById("attendanceStatus");

  if (participantName) {
    participantName.textContent = data.nama || "-";
  }

  if (participantNik) {
    participantNik.textContent = data.nik || "-";
  }

  if (participantDepartment) {
    participantDepartment.textContent = data.departemen || "-";
  }

  if (arrivalTime) {
    arrivalTime.textContent = formatDateTime(data.waktu_datang);
  }

  if (status) {
    status.textContent = "HADIR";

    status.className = "badge bg-success";
  }

  const nikInput = document.getElementById("nikInput");

  if (nikInput) {
    nikInput.value = "";

    nikInput.focus();
  }
}

// ============================================================
// ATTENDANCE - ALREADY ATTENDED
// ============================================================

function showAlreadyAttended(data) {
  const scanResult = document.getElementById("scanResult");

  const participantResult = document.getElementById("participantResult");

  const scanError = document.getElementById("scanError");

  const alreadyAttended = document.getElementById("alreadyAttended");

  if (!scanResult || !participantResult || !scanError || !alreadyAttended) {
    return;
  }

  scanResult.classList.add("d-none");

  scanError.classList.add("d-none");

  participantResult.classList.remove("d-none");

  alreadyAttended.classList.remove("d-none");

  const participantName = document.getElementById("participantName");

  const participantNik = document.getElementById("participantNik");

  const participantDepartment = document.getElementById(
    "participantDepartment",
  );

  const arrivalTime = document.getElementById("arrivalTime");

  if (participantName) {
    participantName.textContent = data.nama || "-";
  }

  if (participantNik) {
    participantNik.textContent = data.nik || "-";
  }

  if (participantDepartment) {
    participantDepartment.textContent = data.departemen || "-";
  }

  if (arrivalTime) {
    arrivalTime.textContent = formatDateTime(data.waktu_datang);
  }

  const status = document.getElementById("attendanceStatus");

  if (status) {
    status.textContent = "SUDAH HADIR";

    status.className = "badge bg-warning text-dark";
  }

  const nikInput = document.getElementById("nikInput");

  if (nikInput) {
    nikInput.value = "";

    nikInput.focus();
  }
}

// ============================================================
// ATTENDANCE - NOT FOUND
// ============================================================

function showAttendanceError() {
  const scanResult = document.getElementById("scanResult");

  const participantResult = document.getElementById("participantResult");

  const scanError = document.getElementById("scanError");

  const alreadyAttended = document.getElementById("alreadyAttended");

  if (!scanResult || !participantResult || !scanError || !alreadyAttended) {
    return;
  }

  scanResult.classList.add("d-none");

  participantResult.classList.add("d-none");

  alreadyAttended.classList.add("d-none");

  scanError.classList.remove("d-none");

  const nikInput = document.getElementById("nikInput");

  if (nikInput) {
    nikInput.select();
  }
}

// ============================================================
// ATTENDANCE - LOAD DATA
// ============================================================

async function fetchAttendanceData() {
  try {
    const response = await fetch("index.php?action=attendance-list");

    if (!response.ok) {
      throw new Error(`HTTP ${response.status}`);
    }

    const result = await response.json();

    if (!result.success) {
      throw new Error(result.message || "Gagal mengambil data kehadiran.");
    }

    return Array.isArray(result.data) ? result.data : [];
  } catch (error) {
    console.error("Gagal mengambil attendance:", error);

    return [];
  }
}

// ============================================================
// ATTENDANCE - LOAD LIST
// ============================================================

async function loadAttendanceList() {
  const table = document.getElementById("attendanceTable");

  const count = document.getElementById("attendanceCount");

  const attendance = await fetchAttendanceData();

  /*
   * Simpan peserta untuk kebutuhan
   * animasi Lucky Draw.
   */
  attendancePool = attendance;

  if (!table || !count) {
    return;
  }

  count.textContent = `${attendance.length} Peserta`;

  if (attendance.length === 0) {
    table.innerHTML = `
            <tr>
                <td
                    colspan="6"
                    class="text-center text-muted py-4"
                >
                    Belum ada peserta yang melakukan scan.
                </td>
            </tr>
        `;

    return;
  }

  /*
   * Bangun semua baris ke dalam array dulu,
   * baru gabungkan dan render SEKALI ke DOM.
   *
   * Kalau pakai innerHTML += di dalam loop,
   * setiap iterasi browser harus re-parse
   * ULANG seluruh HTML yang sudah menumpuk
   * dari awal - jadi lambat secara kuadratik
   * begitu jumlah baris banyak (ratusan-
   * ribuan peserta).
   */
  const rows = attendance.map(function (item, index) {
    const waktu = formatDateTime(item.waktu_datang);

    return `
            <tr>

                <td>
                    ${index + 1}
                </td>

                <td>
                    ${escapeHtml(item.nik)}
                </td>

                <td>
                    ${escapeHtml(item.nama)}
                </td>

                <td>
                    ${escapeHtml(item.departemen)}
                </td>

                <td>
                    ${escapeHtml(waktu)}
                </td>

                <td>
                    <span class="badge bg-success">
                        ${escapeHtml(item.status)}
                    </span>
                </td>

            </tr>
        `;
  });

  table.innerHTML = rows.join("");
}

// ============================================================
// LUCKY DRAW - ANIMATION STYLES (BLUR EFFECT)
// ============================================================

function injectLuckyDrawAnimationStyles() {
  if (document.getElementById("luckyDrawAnimationStyles")) {
    return;
  }

  const style = document.createElement("style");

  style.id = "luckyDrawAnimationStyles";

  style.textContent = `
        .winner-shuffle-blur {
            animation: winnerShuffleBlur 0.12s ease-out;
        }

        @keyframes winnerShuffleBlur {
            0% {
                filter: blur(10px);
                opacity: 0.35;
            }

            60% {
                filter: blur(3px);
                opacity: 0.8;
            }

            100% {
                filter: blur(0);
                opacity: 1;
            }
        }
    `;

  document.head.appendChild(style);
}

// ============================================================
// LUCKY DRAW
// ============================================================

function initLuckyDraw() {
  const modeAuto = document.getElementById("modeAuto");

  const modeManual = document.getElementById("modeManual");

  if (!modeAuto || !modeManual) {
    return;
  }

  /*
   * Suntikkan CSS efek blur untuk animasi
   * pengacakan nama, hanya sekali.
   */
  injectLuckyDrawAnimationStyles();

  const autoMode = document.getElementById("autoMode");

  const manualMode = document.getElementById("manualMode");

  const autoButton = document.getElementById("autoDrawButton");

  const manualButton = document.getElementById("manualDrawButton");

  if (!autoMode || !manualMode || !autoButton || !manualButton) {
    console.error("Elemen Lucky Draw tidak lengkap.");

    return;
  }

  // ========================================================
  // STATE
  // ========================================================

  let autoRunning = false;

  let manualRunning = false;

  let autoTimer = null;

  let manualTimer = null;

  let autoAnimationResolve = null;

  let manualPrize = "";

  // ========================================================
  // LOAD WINNER LIST
  // ========================================================

  loadWinnerList();

  // ========================================================
  // MODE AUTO
  // ========================================================

  modeAuto.addEventListener("change", function () {
    if (!this.checked) {
      return;
    }

    stopAutoDraw();

    stopManualDraw();

    autoMode.classList.remove("d-none");

    manualMode.classList.add("d-none");
  });

  // ========================================================
  // MODE MANUAL
  // ========================================================

  modeManual.addEventListener("change", function () {
    if (!this.checked) {
      return;
    }

    stopAutoDraw();

    stopManualDraw();

    autoMode.classList.add("d-none");

    manualMode.classList.remove("d-none");
  });

  // ========================================================
  // AUTO BUTTON
  // ========================================================

  autoButton.addEventListener("click", function () {
    if (autoRunning) {
      stopAutoDraw();

      return;
    }

    startAutoDraw();
  });

  // ========================================================
  // START AUTO DRAW
  // ========================================================

  async function startAutoDraw() {
    const prizeElement = document.getElementById("autoPrize");

    const winnerCountElement = document.getElementById("winnerCount");

    const intervalElement = document.getElementById("drawInterval");

    if (!prizeElement || !winnerCountElement || !intervalElement) {
      alert("Konfigurasi Auto Draw tidak ditemukan.");

      return;
    }

    const hadiah = prizeElement.value.trim();

    const jumlah = parseInt(winnerCountElement.value, 10);

    const interval = parseInt(intervalElement.value, 10);

    // ====================================================
    // VALIDASI
    // ====================================================

    if (!hadiah) {
      alert("Silakan isi hadiah.");

      prizeElement.focus();

      return;
    }

    if (!jumlah || jumlah < 1) {
      alert("Banyak pemenang harus minimal 1.");

      winnerCountElement.focus();

      return;
    }

    if (!interval || interval < 1) {
      alert("Interval harus minimal 1 detik.");

      intervalElement.focus();

      return;
    }

    /*
     * Pastikan peserta tersedia.
     */
    if (getEligibleParticipants().length < jumlah) {
      alert(
        "Jumlah peserta yang tersedia " +
          "tidak mencukupi untuk jumlah pemenang.",
      );

      return;
    }

    // ====================================================
    // START
    // ====================================================

    autoRunning = true;

    setAutoButtonStop();

    /*
     * Animasi berjalan SELAMA interval.
     *
     * Tidak ada request database selama
     * animasi berlangsung.
     */
    const animationFinished = await animateLuckyDraw(interval * 1000);

    /*
     * Kalau operator menekan STOP sebelum
     * interval selesai, draw dibatalkan.
     */
    if (!animationFinished) {
      return;
    }

    /*
     * Pastikan state masih aktif.
     */
    if (!autoRunning) {
      return;
    }

    /*
     * Animasi selesai.
     *
     * SEKARANG baru request ke database
     * untuk mengambil N pemenang sekaligus.
     */
    autoButton.disabled = true;

    autoButton.textContent = "Memproses...";

    const success = await executeBatchDraw(hadiah, jumlah);

    autoButton.disabled = false;

    if (!success) {
      stopAutoDraw();

      return;
    }

    stopAutoDraw();
  }

  // ========================================================
  // AUTO ANIMATION
  // ========================================================

  function animateLuckyDraw(duration) {
    return new Promise(function (resolve) {
      const startTime = performance.now();

      autoAnimationResolve = resolve;

      function tick() {
        if (!autoRunning) {
          autoAnimationResolve = null;

          resolve(false);

          return;
        }

        showRandomAnimationName();

        const elapsed = performance.now() - startTime;

        if (elapsed >= duration) {
          autoAnimationResolve = null;

          resolve(true);

          return;
        }

        /*
         * Tidak ada batas maksimum durasi.
         *
         * Timer terus berjalan sampai
         * duration selesai.
         */
        autoTimer = setTimeout(tick, 2);
      }

      tick();
    });
  }

  // ========================================================
  // STOP AUTO
  // ========================================================

  function stopAutoDraw() {
    const wasRunning = autoRunning;

    autoRunning = false;

    if (autoTimer !== null) {
      clearTimeout(autoTimer);

      autoTimer = null;
    }

    /*
     * Batalkan Promise animasi jika
     * masih sedang berjalan.
     */
    if (autoAnimationResolve) {
      const resolve = autoAnimationResolve;

      autoAnimationResolve = null;

      resolve(false);
    }

    setAutoButtonStart();

    /*
     * Jangan melakukan apa-apa lagi.
     *
     * wasRunning hanya untuk kejelasan
     * state, bukan untuk melakukan draw.
     */
    if (wasRunning) {
      console.log("Auto Draw dihentikan.");
    }
  }

  // ========================================================
  // AUTO BUTTON - START
  // ========================================================

  function setAutoButtonStart() {
    autoButton.textContent = "Mulai Draw";

    autoButton.classList.remove("btn-danger");

    autoButton.classList.add("btn-primary");
  }

  // ========================================================
  // AUTO BUTTON - STOP
  // ========================================================

  function setAutoButtonStop() {
    autoButton.textContent = "STOP";

    autoButton.classList.remove("btn-primary");

    autoButton.classList.add("btn-danger");
  }

  // ========================================================
  // MANUAL BUTTON
  // ========================================================

  manualButton.addEventListener("click", function () {
    if (manualRunning) {
      stopManualDraw();

      return;
    }

    startManualDraw();
  });

  // ========================================================
  // START MANUAL DRAW
  // ========================================================

  function startManualDraw() {
    const prizeElement = document.getElementById("manualPrize");

    if (!prizeElement) {
      alert("Input hadiah Manual tidak ditemukan.");

      return;
    }

    const hadiah = prizeElement.value.trim();

    if (!hadiah) {
      alert("Silakan isi hadiah.");

      prizeElement.focus();

      return;
    }

    /*
     * Pastikan masih ada peserta yang tersedia
     * SEBELUM animasi dimulai, supaya error
     * langsung ketahuan saat tombol DRAW
     * ditekan, bukan menunggu tombol STOP.
     */
    if (getEligibleParticipants().length < 1) {
      alert(
        "Semua peserta yang hadir sudah mendapatkan " +
          "hadiah. Tidak ada lagi peserta yang tersedia " +
          "untuk diundi.",
      );

      return;
    }

    /*
     * Simpan hadiah untuk digunakan
     * ketika tombol STOP ditekan.
     */
    manualPrize = hadiah;

    /*
     * Mulai animasi tanpa batas waktu.
     */
    manualRunning = true;

    setManualButtonStop();

    startManualAnimation();
  }

  // ========================================================
  // MANUAL ANIMATION
  // ========================================================

  function startManualAnimation() {
    if (!manualRunning) {
      return;
    }

    showRandomAnimationName();

    /*
     * Animasi berjalan terus sampai
     * operator menekan STOP.
     */
    manualTimer = setTimeout(startManualAnimation, 2);
  }

  // ========================================================
  // STOP MANUAL DRAW
  // ========================================================

  async function stopManualDraw() {
    if (!manualRunning) {
      return;
    }

    manualRunning = false;

    if (manualTimer !== null) {
      clearTimeout(manualTimer);

      manualTimer = null;
    }

    setManualButtonStart();

    /*
     * Setelah STOP, baru ambil 1 pemenang
     * dari database.
     */
    manualButton.disabled = true;

    manualButton.textContent = "Memproses...";

    const success = await executeManualDraw(manualPrize);

    manualButton.disabled = false;

    /*
     * Pastikan tombol kembali menjadi DRAW.
     */
    setManualButtonStart();

    if (!success) {
      return;
    }
  }

  // ========================================================
  // MANUAL BUTTON - START
  // ========================================================

  function setManualButtonStart() {
    manualButton.textContent = "DRAW";

    manualButton.classList.remove("btn-danger");

    manualButton.classList.add("btn-primary");
  }

  // ========================================================
  // MANUAL BUTTON - STOP
  // ========================================================

  function setManualButtonStop() {
    manualButton.textContent = "STOP";

    manualButton.classList.remove("btn-primary");

    manualButton.classList.add("btn-danger");
  }

  // ========================================================
  // RANDOM ANIMATION NAME
  // ========================================================

  function showRandomAnimationName() {
    const container = document.getElementById("currentWinner");

    if (!container) {
      return;
    }

    const eligible = getEligibleParticipants();

    if (eligible.length === 0) {
      container.innerHTML = `
                <div class="winner-placeholder">

                    <div class="winner-icon">
                        ?
                    </div>

                    <h4 class="mt-3 mb-1">
                        Tidak ada peserta
                    </h4>

                    <p class="text-muted mb-0">
                        Belum ada peserta yang dapat diundi.
                    </p>

                </div>
            `;

      return;
    }

    const randomIndex = Math.floor(Math.random() * eligible.length);

    const participant = eligible[randomIndex];

    container.innerHTML = `

            <div class="winner-animation winner-shuffle-blur">

                <div class="winner-icon">
                    ?
                </div>

                <div class="winner-name mt-3">
                    ${escapeHtml(participant.nama)}
                </div>

                <div class="winner-department">
                    ${escapeHtml(participant.departemen)}
                </div>

                <div class="mt-2 text-muted">
                    NIK:
                    ${escapeHtml(participant.nik)}
                </div>

            </div>

        `;
  }

  // ========================================================
  // GET ELIGIBLE PARTICIPANTS
  // ========================================================

  function getEligibleParticipants() {
    return attendancePool.filter(function (participant) {
      const id = Number(participant.peserta_id);

      return !winnerParticipantIds.has(id);
    });
  }

  // ========================================================
  // MANUAL DRAW - DATABASE
  // ========================================================

  async function executeManualDraw(hadiah) {
    try {
      const response = await fetch("index.php?action=draw-winner", {
        method: "POST",

        headers: {
          "Content-Type": "application/json",
        },

        body: JSON.stringify({
          hadiah: hadiah,
        }),
      });

      if (!response.ok) {
        throw new Error(`HTTP ${response.status}`);
      }

      const result = await response.json();

      console.log("Manual draw response:", result);

      if (!result.success) {
        alert(result.message || "Draw gagal.");

        return false;
      }

      if (!result.winner) {
        alert("Server tidak mengembalikan " + "data pemenang.");

        return false;
      }

      /*
       * Tambahkan peserta ke daftar pemenang
       * supaya tidak ikut animasi berikutnya.
       */
      winnerParticipantIds.add(Number(result.winner.peserta_id));

      showCurrentWinner(result.winner);

      addWinnerToList(result.winner);

      return true;
    } catch (error) {
      console.error("Manual draw error:", error);

      alert("Tidak dapat memproses Lucky Draw.");

      return false;
    }
  }

  // ========================================================
  // AUTO DRAW - DATABASE
  // ========================================================

  async function executeBatchDraw(hadiah, jumlah) {
    try {
      const response = await fetch("index.php?action=draw-winner-batch", {
        method: "POST",

        headers: {
          "Content-Type": "application/json",
        },

        body: JSON.stringify({
          hadiah: hadiah,

          jumlah: jumlah,
        }),
      });

      if (!response.ok) {
        throw new Error(`HTTP ${response.status}`);
      }

      const result = await response.json();

      console.log("Batch draw response:", result);

      if (!result.success) {
        alert(result.message || "Auto Draw gagal.");

        return false;
      }

      if (!Array.isArray(result.winners)) {
        alert("Server tidak mengembalikan " + "daftar pemenang.");

        return false;
      }

      /*
       * Tandai seluruh pemenang sebagai
       * sudah menang.
       */
      result.winners.forEach(function (winner) {
        winnerParticipantIds.add(Number(winner.peserta_id));
      });

      /*
       * Tampilkan SEMUA pemenang
       * secara bersamaan.
       */
      showCurrentWinners(result.winners);

      /*
       * Tambahkan semua ke sidebar.
       */
      addWinnersToList(result.winners);

      return true;
    } catch (error) {
      console.error("Batch draw error:", error);

      alert("Tidak dapat memproses Auto Draw.");

      return false;
    }
  }
}

// ============================================================
// CURRENT WINNER - SINGLE
// ============================================================

function showCurrentWinner(winner) {
  const container = document.getElementById("currentWinner");

  if (!container) {
    return;
  }

  container.innerHTML = `

        <div>

            <div class="winner-icon">
                ✓
            </div>

            <div class="winner-name mt-3">
                ${escapeHtml(winner.nama)}
            </div>

            <div class="winner-department">
                ${escapeHtml(winner.departemen)}
            </div>

            <div class="mt-2">
                NIK:
                ${escapeHtml(winner.nik)}
            </div>

            <div class="winner-prize">
                ${escapeHtml(winner.hadiah)}
            </div>

        </div>

    `;
}

// ============================================================
// CURRENT WINNER - BATCH
// ============================================================

function showCurrentWinners(winners) {
  const container = document.getElementById("currentWinner");

  if (!container) {
    return;
  }

  if (!winners || winners.length === 0) {
    container.innerHTML = `
            <div class="winner-placeholder">
                Tidak ada pemenang.
            </div>
        `;

    return;
  }

  container.innerHTML = `

        <div class="winner-batch">

            <div class="mb-4">

                <h3 class="mb-1">
                    ${winners.length} Pemenang
                </h3>

                <p class="text-muted mb-0">
                    Pemenang telah berhasil dipilih.
                </p>

            </div>


            <div class="winner-batch-grid">

                ${winners
                  .map(function (winner, index) {
                    return `

                            <div class="winner-batch-card">

                                <div
                                    class="winner-batch-number"
                                >
                                    Pemenang #${index + 1}
                                </div>

                                <div
                                    class="winner-batch-name"
                                >
                                    ${escapeHtml(winner.nama)}
                                </div>

                                <div
                                    class="winner-batch-department"
                                >
                                    ${escapeHtml(winner.departemen)}
                                </div>

                                <div class="mt-2 text-muted">
                                    NIK:
                                    ${escapeHtml(winner.nik)}
                                </div>

                                <div
                                    class="winner-prize"
                                >
                                    ${escapeHtml(winner.hadiah)}
                                </div>

                            </div>

                        `;
                  })
                  .join("")}

            </div>

        </div>

    `;
}

// ============================================================
// WINNER LIST - LOAD FROM DATABASE
// ============================================================

async function loadWinnerList() {
  const list = document.getElementById("winnerList");

  const badge = document.getElementById("winnerCountBadge");

  if (!list) {
    return;
  }

  try {
    const response = await fetch("index.php?action=winner-list");

    if (!response.ok) {
      throw new Error(`HTTP ${response.status}`);
    }

    const result = await response.json();

    console.log("Winner list:", result);

    if (!result.success) {
      throw new Error(result.message || "Gagal mengambil daftar pemenang.");
    }

    const winners = Array.isArray(result.data) ? result.data : [];

    /*
     * Update ID peserta yang sudah menang.
     */
    winnerParticipantIds.clear();

    winners.forEach(function (winner) {
      winnerParticipantIds.add(Number(winner.peserta_id));
    });

    /*
     * Update sidebar.
     */
    renderWinnerList(winners);

    if (badge) {
      badge.textContent = winners.length;
    }
  } catch (error) {
    console.error("Gagal mengambil daftar pemenang:", error);
  }
}

// ============================================================
// WINNER LIST - RENDER
// ============================================================

function renderWinnerList(winners) {
  const list = document.getElementById("winnerList");

  const badge = document.getElementById("winnerCountBadge");

  if (!list) {
    return;
  }

  if (!winners || winners.length === 0) {
    list.innerHTML = `

            <div
                class="text-center text-muted py-5"
            >
                Belum ada pemenang.
            </div>

        `;

    if (badge) {
      badge.textContent = "0";
    }

    return;
  }

  list.innerHTML = "";

  winners.forEach(function (winner, index) {
    const item = createWinnerListItem(winner, winners.length - index);

    list.appendChild(item);
  });

  if (badge) {
    badge.textContent = winners.length;
  }
}

// ============================================================
// WINNER LIST - CREATE ITEM
// ============================================================

function createWinnerListItem(winner, number) {
  const item = document.createElement("div");

  item.className = "winner-item";

  item.innerHTML = `

        <div class="winner-item-number">
            Pemenang #${number}
        </div>

        <div class="winner-item-name">
            ${escapeHtml(winner.nama)}
        </div>

        <div>
            ${escapeHtml(winner.nik)}
            -
            ${escapeHtml(winner.departemen)}
        </div>

        <div class="winner-item-prize">
            Hadiah:
            ${escapeHtml(winner.hadiah)}
        </div>

    `;

  return item;
}

// ============================================================
// WINNER LIST - ADD SINGLE
// ============================================================

function addWinnerToList(winner) {
  const list = document.getElementById("winnerList");

  const badge = document.getElementById("winnerCountBadge");

  if (!list) {
    return;
  }

  /*
   * Kalau masih placeholder,
   * hapus terlebih dahulu.
   */
  const placeholder = list.querySelector(".text-center.text-muted");

  if (placeholder) {
    placeholder.remove();
  }

  const currentCount = list.querySelectorAll(".winner-item").length;

  const item = createWinnerListItem(winner, currentCount + 1);

  list.prepend(item);

  if (badge) {
    badge.textContent = list.querySelectorAll(".winner-item").length;
  }
}

// ============================================================
// WINNER LIST - ADD BATCH
// ============================================================

function addWinnersToList(winners) {
  if (!Array.isArray(winners) || winners.length === 0) {
    return;
  }

  /*
   * Setelah batch berhasil,
   * paling aman reload dari database.
   *
   * Dengan begitu urutan dan data sidebar
   * mengikuti PostgreSQL sebagai source of truth.
   */
  loadWinnerList();
}

// ============================================================
// INITIALIZE APPLICATION
// ============================================================

// ============================================================
// PRIZE PICKUP - SCAN HADIAH
// ============================================================

let currentPrizeWinnerId = null;

// ============================================================
// PRIZE PICKUP - CHECK / VERIFY NIK
// ============================================================

function initPrizeCheck() {
  const form = document.getElementById("prizeCheckForm");

  const nikInput = document.getElementById("prizeNikInput");

  if (!form || !nikInput) {
    return;
  }

  form.addEventListener("submit", async function (event) {
    event.preventDefault();

    const nik = nikInput.value.trim();

    if (nik === "") {
      nikInput.focus();

      return;
    }

    try {
      const response = await fetch("index.php?action=check-prize", {
        method: "POST",

        headers: {
          "Content-Type": "application/json",
        },

        body: JSON.stringify({
          nik: nik,
        }),
      });

      if (!response.ok) {
        throw new Error(`HTTP ${response.status}`);
      }

      const result = await response.json();

      console.log("Check prize response:", result);

      // ====================================================
      // PEMENANG DITEMUKAN
      // ====================================================

      if (result.success && result.winner) {
        showPrizeResult(result.winner);

        return;
      }

      // ====================================================
      // BUKAN PEMENANG
      // ====================================================

      if (result.type === "not_winner") {
        showPrizeError(
          "Bukan Pemenang",
          result.message || "Peserta bukan merupakan pemenang Lucky Draw.",
        );

        return;
      }

      showPrizeError(
        "Terjadi Kesalahan",
        result.message || "Tidak dapat memverifikasi peserta.",
      );
    } catch (error) {
      console.error("Check prize error:", error);

      showPrizeError(
        "Terjadi Kesalahan",
        "Tidak dapat memproses permintaan ke server.",
      );
    }
  });
}

// ============================================================
// PRIZE PICKUP - SHOW RESULT
// ============================================================

function showPrizeResult(winner) {
  const empty = document.getElementById("prizeEmpty");

  const result = document.getElementById("prizeResult");

  const error = document.getElementById("prizeError");

  if (!empty || !result || !error) {
    return;
  }

  empty.classList.add("d-none");

  error.classList.add("d-none");

  result.classList.remove("d-none");

  currentPrizeWinnerId = Number(winner.id);

  const nameEl = document.getElementById("prizeParticipantName");

  const departmentEl = document.getElementById("prizeParticipantDepartment");

  const nikEl = document.getElementById("prizeParticipantNik");

  const hadiahEl = document.getElementById("prizeName");

  const statusEl = document.getElementById("prizeStatus");

  const actionEl = document.getElementById("prizeAction");

  if (nameEl) {
    nameEl.textContent = winner.nama || "-";
  }

  if (departmentEl) {
    departmentEl.textContent = winner.departemen || "-";
  }

  if (nikEl) {
    nikEl.textContent = winner.nik || "-";
  }

  if (hadiahEl) {
    hadiahEl.textContent = winner.hadiah || "-";
  }

  const sudahDiambil = winner.status_pengambilan === "DIAMBIL";

  if (statusEl) {
    if (sudahDiambil) {
      statusEl.innerHTML = `
                <div class="alert alert-secondary mb-0">
                    <strong>Sudah Diambil</strong> —
                    diambil pada
                    ${escapeHtml(formatDateTime(winner.waktu_pengambilan))}
                </div>
            `;
    } else {
      statusEl.innerHTML = `
                <div class="alert alert-success mb-0">
                    <strong>Pemenang Terverifikasi</strong> —
                    hadiah belum diambil.
                </div>
            `;
    }
  }

  if (actionEl) {
    if (sudahDiambil) {
      actionEl.innerHTML = `
                <button
                    type="button"
                    class="btn btn-secondary btn-lg w-100"
                    disabled
                >
                    Hadiah Sudah Diambil
                </button>
            `;
    } else {
      actionEl.innerHTML = `
                <button
                    type="button"
                    id="takePrizeButton"
                    class="btn btn-success btn-lg w-100"
                >
                    Konfirmasi Pengambilan Hadiah
                </button>
            `;

      bindTakePrizeButton();
    }
  }

  const nikInput = document.getElementById("prizeNikInput");

  if (nikInput) {
    nikInput.value = "";

    nikInput.focus();
  }
}

// ============================================================
// PRIZE PICKUP - SHOW ERROR
// ============================================================

function showPrizeError(title, message) {
  const empty = document.getElementById("prizeEmpty");

  const result = document.getElementById("prizeResult");

  const error = document.getElementById("prizeError");

  if (!empty || !result || !error) {
    return;
  }

  empty.classList.add("d-none");

  result.classList.add("d-none");

  error.classList.remove("d-none");

  currentPrizeWinnerId = null;

  const titleEl = document.getElementById("prizeErrorTitle");

  const messageEl = document.getElementById("prizeErrorMessage");

  if (titleEl) {
    titleEl.textContent = title;
  }

  if (messageEl) {
    messageEl.textContent = message;
  }

  const nikInput = document.getElementById("prizeNikInput");

  if (nikInput) {
    nikInput.select();
  }
}

// ============================================================
// PRIZE PICKUP - CONFIRM TAKE PRIZE
// ============================================================

function bindTakePrizeButton() {
  const button = document.getElementById("takePrizeButton");

  if (!button) {
    return;
  }

  button.addEventListener("click", async function () {
    if (!currentPrizeWinnerId) {
      alert("Data pemenang tidak ditemukan.");

      return;
    }

    button.disabled = true;

    button.textContent = "Memproses...";

    try {
      const response = await fetch("index.php?action=take-prize", {
        method: "POST",

        headers: {
          "Content-Type": "application/json",
        },

        body: JSON.stringify({
          pemenang_id: currentPrizeWinnerId,
        }),
      });

      if (!response.ok) {
        throw new Error(`HTTP ${response.status}`);
      }

      const result = await response.json();

      console.log("Take prize response:", result);

      // ====================================================
      // SUDAH DIAMBIL SEBELUMNYA / GAGAL
      // ====================================================

      if (!result.success) {
        alert(result.message || "Gagal mengonfirmasi pengambilan hadiah.");

        if (result.winner) {
          showPrizeResult(result.winner);
        } else {
          button.disabled = false;

          button.textContent = "Konfirmasi Pengambilan Hadiah";
        }

        return;
      }

      // ====================================================
      // BERHASIL
      // ====================================================

      showPrizeResult(result.winner);

      loadPrizeWinnerList();
    } catch (error) {
      console.error("Take prize error:", error);

      alert("Tidak dapat memproses konfirmasi pengambilan hadiah.");

      button.disabled = false;

      button.textContent = "Konfirmasi Pengambilan Hadiah";
    }
  });
}

// ============================================================
// PRIZE PICKUP - WINNER / PICKUP LIST
// ============================================================

async function loadPrizeWinnerList() {
  const table = document.getElementById("prizeWinnerTable");

  const badge = document.getElementById("prizeWinnerCount");

  if (!table) {
    return;
  }

  try {
    const response = await fetch("index.php?action=winner-list");

    if (!response.ok) {
      throw new Error(`HTTP ${response.status}`);
    }

    const result = await response.json();

    if (!result.success) {
      throw new Error(result.message || "Gagal mengambil daftar pemenang.");
    }

    const winners = Array.isArray(result.data) ? result.data : [];

    if (badge) {
      badge.textContent = `${winners.length} Pemenang`;
    }

    if (winners.length === 0) {
      table.innerHTML = `
                <tr>
                    <td
                        colspan="8"
                        class="text-center text-muted py-4"
                    >
                        Belum ada pemenang.
                    </td>
                </tr>
            `;

      return;
    }

    /*
     * Bangun semua baris ke array dulu, baru
     * gabungkan dan render sekali ke DOM
     * (hindari innerHTML += di dalam loop).
     */
    const rows = winners.map(function (winner, index) {
      const sudahDiambil = winner.status_pengambilan === "DIAMBIL";

      const statusBadge = sudahDiambil
        ? '<span class="badge bg-success">Sudah Diambil</span>'
        : '<span class="badge bg-warning text-dark">Belum Diambil</span>';

      return `
                <tr>

                    <td>
                        ${index + 1}
                    </td>

                    <td>
                        ${escapeHtml(winner.nik)}
                    </td>

                    <td>
                        ${escapeHtml(winner.nama)}
                    </td>

                    <td>
                        ${escapeHtml(winner.departemen)}
                    </td>

                    <td>
                        ${escapeHtml(winner.hadiah)}
                    </td>

                    <td>
                        ${escapeHtml(formatDateTime(winner.waktu_menang))}
                    </td>

                    <td>
                        ${
                          winner.waktu_pengambilan
                            ? escapeHtml(
                                formatDateTime(winner.waktu_pengambilan),
                              )
                            : "-"
                        }
                    </td>

                    <td>
                        ${statusBadge}
                    </td>

                </tr>
            `;
    });

    table.innerHTML = rows.join("");
  } catch (error) {
    console.error("Gagal mengambil daftar pemenang:", error);
  }
}

// ============================================================
// DASHBOARD - WINNER OVERVIEW
// ============================================================

let dashboardRefreshTimer = null;

let dashboardWinnersData = [];

let dashboardSearchTerm = "";

function initDashboardWinners() {
  const container = document.getElementById("dashboardWinnerGroups");

  if (!container) {
    return;
  }

  loadDashboardWinners();

  initDashboardSearch();

  /*
   * Refresh otomatis supaya layar dashboard
   * selalu menampilkan data terbaru tanpa
   * perlu reload manual.
   */
  if (dashboardRefreshTimer !== null) {
    clearInterval(dashboardRefreshTimer);
  }

  dashboardRefreshTimer = setInterval(loadDashboardWinners, 5000);
}

// ============================================================
// DASHBOARD - LOAD DATA
// ============================================================

async function loadDashboardWinners() {
  const container = document.getElementById("dashboardWinnerGroups");

  if (!container) {
    return;
  }

  try {
    const response = await fetch("index.php?action=winner-list");

    if (!response.ok) {
      throw new Error(`HTTP ${response.status}`);
    }

    const result = await response.json();

    if (!result.success) {
      throw new Error(result.message || "Gagal mengambil data pemenang.");
    }

    dashboardWinnersData = Array.isArray(result.data) ? result.data : [];

    /*
     * Terapkan ulang kata kunci pencarian yang sedang
     * aktif (kalau ada) setiap kali data di-refresh,
     * supaya hasil pencarian tidak hilang tiba-tiba.
     */
    applyDashboardSearchFilter();
  } catch (error) {
    console.error("Gagal mengambil data pemenang:", error);
  }
}

// ============================================================
// DASHBOARD - SEARCH
// ============================================================

function initDashboardSearch() {
  const input = document.getElementById("dashboardWinnerSearch");

  const clearButton = document.getElementById("dashboardWinnerSearchClear");

  if (!input) {
    return;
  }

  input.addEventListener("input", function () {
    dashboardSearchTerm = this.value;

    if (clearButton) {
      clearButton.classList.toggle("d-none", this.value.trim() === "");
    }

    applyDashboardSearchFilter();
  });

  /*
   * Jeda auto-scroll selagi user mengetik pencarian,
   * supaya halaman tidak scroll sendiri di saat yang
   * tidak diinginkan.
   */
  input.addEventListener("focus", function () {
    stopAutoScroll();
  });

  input.addEventListener("blur", function () {
    if (autoScrollEnabled) {
      startAutoScroll();
    }
  });

  if (clearButton) {
    clearButton.addEventListener("click", function () {
      input.value = "";

      dashboardSearchTerm = "";

      clearButton.classList.add("d-none");

      applyDashboardSearchFilter();

      input.focus();
    });
  }
}

// ============================================================
// DASHBOARD - APPLY SEARCH FILTER
// ============================================================

function applyDashboardSearchFilter() {
  const term = dashboardSearchTerm.trim();

  let filtered = dashboardWinnersData;

  if (term !== "") {
    const lowerTerm = term.toLowerCase();

    filtered = dashboardWinnersData.filter(function (winner) {
      const nik = (winner.nik || "").toLowerCase();

      const nama = (winner.nama || "").toLowerCase();

      return nik.includes(lowerTerm) || nama.includes(lowerTerm);
    });
  }

  renderDashboardWinners(filtered, term);

  updateDashboardSearchInfo(
    term,
    filtered.length,
    dashboardWinnersData.length,
  );
}

// ============================================================
// DASHBOARD - SEARCH RESULT INFO TEXT
// ============================================================

function updateDashboardSearchInfo(term, matchCount, totalCount) {
  const info = document.getElementById("dashboardSearchResultInfo");

  if (!info) {
    return;
  }

  if (term === "") {
    info.textContent = "";

    return;
  }

  if (matchCount === 0) {
    info.textContent = `Tidak ada pemenang yang cocok dari ${totalCount} data. Peserta ini kemungkinan belum menang.`;
  } else {
    info.textContent = `Menampilkan ${matchCount} dari ${totalCount} pemenang.`;
  }
}

// ============================================================
// DASHBOARD - HIGHLIGHT PENCARIAN
// ============================================================

function highlightMatch(text, term) {
  const safeText = escapeHtml(text || "");

  if (!term) {
    return safeText;
  }

  const escapedTerm = term.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");

  const regex = new RegExp(`(${escapedTerm})`, "ig");

  return safeText.replace(
    regex,
    '<mark class="dashboard-search-highlight">$1</mark>',
  );
}

// ============================================================
// DASHBOARD - RENDER (GROUP PER HADIAH)
// ============================================================

function renderDashboardWinners(winners, searchTerm) {
  const container = document.getElementById("dashboardWinnerGroups");

  if (!container) {
    return;
  }

  if (!winners || winners.length === 0) {
    if (searchTerm) {
      container.innerHTML = `
                <div class="text-center text-muted py-5">
                    Tidak ditemukan pemenang dengan NIK/nama
                    "${escapeHtml(searchTerm)}".
                </div>
            `;
    } else {
      container.innerHTML = `
                <div class="text-center text-muted py-5">
                    Belum ada pemenang.
                </div>
            `;
    }

    return;
  }

  /*
   * Urutkan dari pemenang paling awal ke paling baru
   * per hadiah, supaya nomor urut (1, 2, 3, ...)
   * tetap konsisten setiap kali data di-refresh.
   */
  const sorted = [...winners].sort(function (a, b) {
    return new Date(a.waktu_menang) - new Date(b.waktu_menang);
  });

  const groups = new Map();

  sorted.forEach(function (winner) {
    const key = winner.hadiah || "Tanpa Nama Hadiah";

    if (!groups.has(key)) {
      groups.set(key, []);
    }

    groups.get(key).push(winner);
  });

  container.innerHTML = "";

  groups.forEach(function (groupWinners, hadiahName) {
    const sudahDiambilCount = groupWinners.filter(function (winner) {
      return winner.status_pengambilan === "DIAMBIL";
    }).length;

    const belumDiambilCount = groupWinners.length - sudahDiambilCount;

    const card = document.createElement("div");

    card.className = "card shadow-sm mb-4";

    card.innerHTML = `
            <div class="card-header d-flex flex-column align-items-center text-center gap-2">

                <h4 class="mb-0">
                    ${escapeHtml(hadiahName)}
                </h4>

                <div class="small">

                    <span class="badge bg-warning text-dark me-1">
                        Belum Diambil: ${belumDiambilCount}
                    </span>

                    <span class="badge bg-success">
                        Sudah Diambil: ${sudahDiambilCount}
                    </span>

                </div>

            </div>

            <div class="card-body">

                <div class="dashboard-winner-list">

                    ${groupWinners
                      .map(function (winner, index) {
                        const sudah =
                          winner.status_pengambilan === "DIAMBIL";

                        const colorClass = sudah
                          ? "dashboard-winner-hijau"
                          : "dashboard-winner-kuning";

                        return `
                                <div class="dashboard-winner-item ${colorClass}">

                                    <div class="dashboard-winner-number">
                                        #${index + 1}
                                    </div>

                                    <div class="dashboard-winner-name">
                                        ${highlightMatch(winner.nama, searchTerm)}
                                    </div>

                                    <div class="dashboard-winner-dept">
                                        ${escapeHtml(winner.departemen)}
                                    </div>

                                    <div class="dashboard-winner-nik">
                                        ${highlightMatch(winner.nik, searchTerm)}
                                    </div>

                                </div>
                            `;
                      })
                      .join("")}

                </div>

            </div>
        `;

    container.appendChild(card);
  });
}

// ============================================================
// DASHBOARD - AUTO SCROLL (TOP <-> BOTTOM, LOOP)
// ============================================================

const AUTO_SCROLL_STEP = 2;

const AUTO_SCROLL_INTERVAL = 20;

const AUTO_SCROLL_PAUSE = 1500;

let autoScrollEnabled = true;

let autoScrollDirection = 1;

let autoScrollTimer = null;

function initAutoScroll() {
  const toggle = document.getElementById("autoScrollToggle");

  if (!toggle) {
    return;
  }

  autoScrollEnabled = toggle.checked;

  toggle.addEventListener("change", function () {
    autoScrollEnabled = this.checked;

    if (autoScrollEnabled) {
      startAutoScroll();
    } else {
      stopAutoScroll();
    }
  });

  if (autoScrollEnabled) {
    startAutoScroll();
  }
}

// ============================================================
// DASHBOARD - AUTO SCROLL - START
// ============================================================

function startAutoScroll() {
  stopAutoScroll();

  autoScrollStep();
}

// ============================================================
// DASHBOARD - AUTO SCROLL - STEP
// ============================================================

function autoScrollStep() {
  if (!autoScrollEnabled) {
    autoScrollTimer = null;

    return;
  }

  const scrollHeight = document.documentElement.scrollHeight;

  const clientHeight = document.documentElement.clientHeight;

  const maxScroll = Math.max(scrollHeight - clientHeight, 0);

  let current = window.scrollY;

  if (autoScrollDirection === 1) {
    current += AUTO_SCROLL_STEP;

    /*
     * Sudah sampai bawah.
     *
     * Berhenti sebentar, lalu balik arah ke atas.
     */
    if (current >= maxScroll) {
      window.scrollTo(0, maxScroll);

      autoScrollDirection = -1;

      autoScrollTimer = setTimeout(autoScrollStep, AUTO_SCROLL_PAUSE);

      return;
    }
  } else {
    current -= AUTO_SCROLL_STEP;

    /*
     * Sudah sampai atas.
     *
     * Berhenti sebentar, lalu balik arah ke bawah.
     */
    if (current <= 0) {
      window.scrollTo(0, 0);

      autoScrollDirection = 1;

      autoScrollTimer = setTimeout(autoScrollStep, AUTO_SCROLL_PAUSE);

      return;
    }
  }

  window.scrollTo(0, current);

  autoScrollTimer = setTimeout(autoScrollStep, AUTO_SCROLL_INTERVAL);
}

// ============================================================
// DASHBOARD - AUTO SCROLL - STOP
// ============================================================

function stopAutoScroll() {
  if (autoScrollTimer !== null) {
    clearTimeout(autoScrollTimer);

    autoScrollTimer = null;
  }
}

// ============================================================
// INITIALIZE APPLICATION
// ============================================================

document.addEventListener("DOMContentLoaded", function () {
  initSidebar();

  initAttendance();

  initLuckyDraw();

  loadAttendanceList();

  initPrizeCheck();

  loadPrizeWinnerList();

  initDashboardWinners();

  initAutoScroll();
});
