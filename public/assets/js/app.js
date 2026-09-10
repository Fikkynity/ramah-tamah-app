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

  table.innerHTML = "";

  attendance.forEach(function (item, index) {
    const waktu = formatDateTime(item.waktu_datang);

    table.innerHTML += `
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

            <div class="winner-animation">

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

document.addEventListener("DOMContentLoaded", function () {
  initSidebar();

  initAttendance();

  initLuckyDraw();

  loadAttendanceList();
});
