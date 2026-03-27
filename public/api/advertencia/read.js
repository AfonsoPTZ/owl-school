async function carregarAdvertencias() {
  const corpoTabela = document.getElementById("tbodyAdvertencias");

  if (!corpoTabela) {
    console.error("Elemento #tbodyAdvertencias não encontrado.");
    return;
  }

  corpoTabela.innerHTML = "";

  try {
    const user = await getSessionUser();

    if (!user) {
      alert("Falha ao carregar dados do usuário.");
      return;
    }

    const tipoUsuario = user.tipo_usuario;

    const response = await fetch("/owl-school/api/advertencia", { method: "GET" });

    if (!response.ok) {
      alert("Erro ao carregar advertências.");
      return;
    }

    const resultado = await response.json();

    if (!resultado.advertencias || resultado.advertencias.length === 0) {
      corpoTabela.insertAdjacentHTML(
        "beforeend",
        `
        <tr>
          <td colspan="3" class="text-center text-muted">Nenhuma advertência encontrada.</td>
        </tr>
      `
      );
      return;
    }

    for (const advertencia of resultado.advertencias) {
      corpoTabela.insertAdjacentHTML(
        "beforeend",
        `
        <tr>
          <td>
            <strong>${advertencia.titulo}</strong><br>
            <small class="text-muted">${advertencia.descricao}</small>
          </td>
          <td>${advertencia.aluno_nome}</td>
          <td class="text-end">
            <button class="btn btn-sm btn-outline-secondary"
              onclick="abrirModalEditarAdvertencia(${advertencia.id})">Editar</button>
            <button class="btn btn-sm btn-outline-danger"
              onclick="excluirAdvertencia(${advertencia.id})">Excluir</button>
          </td>
        </tr>
      `
      );
    }
  } catch (error) {
    console.error("Erro ao carregar advertências:", error);
    alert("Erro de conexão com o servidor.");

    corpoTabela.innerHTML = `
      <tr>
        <td colspan="3" class="text-center text-danger">Erro ao carregar advertências.</td>
      </tr>
    `;
  }
}

document.addEventListener("DOMContentLoaded", carregarAdvertencias);

