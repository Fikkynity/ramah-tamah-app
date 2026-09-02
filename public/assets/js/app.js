document.addEventListener("DOMContentLoaded", function () {
  const sidebar = document.getElementById("sidebar");
  const sidebarToggle = document.getElementById("sidebarToggle");

  if (!sidebar || !sidebarToggle) {
    return;
  }

  sidebarToggle.addEventListener("click", function () {
    sidebar.classList.toggle("collapsed");

    if (sidebar.classList.contains("collapsed")) {
      sidebarToggle.innerHTML = "›";
      sidebarToggle.setAttribute("aria-label", "Tampilkan sidebar");
    } else {
      sidebarToggle.innerHTML = "‹";
      sidebarToggle.setAttribute("aria-label", "Sembunyikan sidebar");
    }
  });
});
