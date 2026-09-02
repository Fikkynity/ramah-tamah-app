<aside class="sidebar" id="sidebar">

    <div class="sidebar-inner p-3">

        <h4 class="text-white mb-4">
            Ramah Tamah
        </h4>

        <ul class="nav nav-pills flex-column gap-2">

            <li class="nav-item">
                <a
                    href="index.php"
                    class="nav-link <?= ($currentPage ?? '') === 'dashboard' ? 'active' : '' ?>">
                    Dashboard
                </a>
            </li>

            <li class="nav-item">
                <a
                    href="index.php?page=scan-hadir"
                    class="nav-link <?= ($currentPage ?? '') === 'scan-hadir' ? 'active' : '' ?>">
                    Scan Daftar Hadir
                </a>
            </li>

            <li class="nav-item">
                <a
                    href="index.php?page=lucky-draw"
                    class="nav-link <?= ($currentPage ?? '') === 'lucky-draw' ? 'active' : '' ?>">
                    Lucky Draw
                </a>
            </li>

            <li class="nav-item">
                <a
                    href="index.php?page=scan-hadiah"
                    class="nav-link <?= ($currentPage ?? '') === 'scan-hadiah' ? 'active' : '' ?>">
                    Scan Pengambilan Hadiah
                </a>
            </li>

        </ul>

    </div>

    <button
        type="button"
        class="sidebar-toggle"
        id="sidebarToggle"
        aria-label="Sembunyikan sidebar">
        ‹
    </button>

</aside>