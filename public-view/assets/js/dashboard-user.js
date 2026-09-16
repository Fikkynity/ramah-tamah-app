// ============================================================
// RAMAH TAMAH - DASHBOARD PUBLIK
// Versi ringkas, HANYA untuk halaman daftar pemenang.
// Tidak ada logic attendance, lucky draw, atau prize pickup
// sama sekali di file ini.
// ============================================================

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
// INITIALIZE
// ============================================================

document.addEventListener("DOMContentLoaded", function () {
  initDashboardWinners();

  initAutoScroll();
});
