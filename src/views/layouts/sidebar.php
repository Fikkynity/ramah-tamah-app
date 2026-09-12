<aside class="sidebar" id="sidebar">

    <div class="sidebar-inner p-3">

        <!-- Brand -->
        <div class="mb-4">
            <h4 class="mb-0">
                <i class="bi bi-balloon-heart me-2"></i>
                Ramah Tamah
            </h4>
        </div>

        <!-- Navigation -->
        <ul class="nav nav-pills flex-column gap-2">

            <!-- Dashboard -->
            <li class="nav-item">
                <a
                    href="index.php"
                    class="nav-link <?= ($currentPage ?? '') === 'dashboard' ? 'active' : '' ?>">

                    <i class="bi bi-speedometer2 me-2"></i>
                    <span>Dashboard</span>

                </a>
            </li>

            <!-- Scan Daftar Hadir -->
            <li class="nav-item">
                <a
                    href="index.php?page=scan-hadir"
                    class="nav-link <?= ($currentPage ?? '') === 'scan-hadir' ? 'active' : '' ?>">

                    <i class="bi bi-qr-code-scan me-2"></i>
                    <span>Scan Daftar Hadir</span>

                </a>
            </li>

            <!-- Lucky Draw -->
            <li class="nav-item">
                <a
                    href="index.php?page=lucky-draw"
                    class="nav-link <?= ($currentPage ?? '') === 'lucky-draw' ? 'active' : '' ?>">

                    <i class="bi bi-trophy me-2"></i>
                    <span>Lucky Draw</span>

                </a>
            </li>

            <!-- Scan Pengambilan Hadiah -->
            <li class="nav-item">
                <a
                    href="index.php?page=scan-hadiah"
                    class="nav-link <?= ($currentPage ?? '') === 'scan-hadiah' ? 'active' : '' ?>">

                    <i class="bi bi-gift me-2"></i>
                    <span>Scan Pengambilan Hadiah</span>

                </a>
            </li>

        </ul>

    </div>

    <!-- Sidebar Toggle -->
    <button
        type="button"
        class="sidebar-toggle"
        id="sidebarToggle"
        aria-label="Sembunyikan sidebar">

        <i class="bi bi-chevron-left" id="sidebarToggleIcon"></i>

    </button>

</aside>