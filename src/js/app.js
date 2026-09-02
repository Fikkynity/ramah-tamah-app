document.addEventListener("DOMContentLoaded", function () {
  const sidebar = document.getElementById("sidebar");
  const sidebarToggle = document.getElementById("sidebarToggle");
  const sidebarToggleIcon = document.getElementById("sidebarToggleIcon");

  if (!sidebar || !sidebarToggle || !sidebarToggleIcon) {
    return;
  }

  sidebarToggle.addEventListener("click", function () {
    sidebar.classList.toggle("collapsed");

    const isCollapsed = sidebar.classList.contains("collapsed");

    sidebarToggleIcon.classList.toggle("bi-chevron-left", !isCollapsed);
    sidebarToggleIcon.classList.toggle("bi-chevron-right", isCollapsed);

    sidebarToggle.setAttribute(
      "aria-label",
      isCollapsed ? "Tampilkan sidebar" : "Sembunyikan sidebar",
    );
  });
});
