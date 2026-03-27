async function carregarChamadas() {
  const corpoTabela = document.getElementById("tbodyChamadas");

  if (!corpoTabela) {
    console.error("Elemento #tbodyChamadas não encontrado.");
    return;
  }

  corpoTabela.innerHTML = "";

  try {
    const user = await getSessionUser();

    if (!user) {
      alert("Falha ao carregar dados do usuário.");
      return;
    }

    const response = await fetch("/owl-school/api/chamada", { method: "GET" });

    if (!response.ok) {
      alert("Erro ao carregar chamadas.");
      return;
    }

    const resultado = await response.json();
    const chamadas = resultado.chamadas || [];

    if (!chamadas.length) {
      corpoTabela.insertAdjacentHTML(
        "beforeend",
        `
        <tr>
          <td colspan="2" class="text-center text-muted">Nenhuma chamada.</td>
        </tr>
      `
      );
      return;
    }

    for (const chamada of chamadas) {
      corpoTabela.insertAdjacentHTML(
        "beforeend",
        `
        <tr>
          <td>${chamada.data}</td>
          <td class="text-end">
            <button class="btn btn-primary btn-sm" onclick="listarItensDaChamada(${chamada.id})">Lançar presença</button>
            <button class="btn btn-sm btn-outline-secondary ms-1" onclick="editarChamada(${chamada.id})">Editar</button>
            <button class="btn btn-sm btn-outline-danger ms-1" onclick="excluirChamada(${chamada.id})">Excluir</button>
          </td>
        </tr>
      `
      );
    }
  } catch (error) {
    console.error("Erro ao carregar chamadas:", error);
    alert("Erro de conexão com o servidor.");

    corpoTabela.innerHTML = `
      <tr>
        <td colspan="2" class="text-center text-danger">Erro ao carregar chamadas.</td>
      </tr>
    `;
  }
}

document.addEventListener("DOMContentLoaded", carregarChamadas);

