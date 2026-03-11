function renderNavbar() {
  const container = document.getElementById("navbar-container");

  if (!container) return;

  container.innerHTML = `
  <nav class="bg-dark text-white p-3 vh-100" style="width:220px; position:fixed;">
    <h4 class="fw-bold mb-4">OlwSchool</h4>

    <ul class="nav flex-column">

      <li class="nav-item mb-2">
        <a class="nav-link text-white" href="responsavel.html">🏠 Início</a>
      </li>

      <li class="nav-item mb-2">
        <a class="nav-link text-white" href="agenda.html">📅 Agenda</a>
      </li>

      <li class="nav-item mb-2">
        <a class="nav-link text-white" href="comunicados.html">📢 Comunicados</a>
      </li>

      <li class="nav-item mb-2">
        <a class="nav-link text-white" href="desempenho.html">📊 Desempenho</a>
      </li>

      <li class="nav-item mt-3">
        <a id="btnLogout" class="nav-link text-danger fw-bold" href="#">🚪 Sair</a>
      </li>

    </ul>
  </nav>
  `;
}

document.addEventListener("DOMContentLoaded", renderNavbar);
