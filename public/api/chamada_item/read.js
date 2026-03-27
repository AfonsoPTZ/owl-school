async function listarItensDaChamada(chamadaId) {
  if (!chamadaId) {
    console.error("ID da chamada não informado.");
    return;
  }

  try {
    const resp = await fetch("/owl-school/api/chamada_item?chamada_id=" + chamadaId, {
      method: "GET"
    });

    if (!resp.ok) {
      alert("Erro ao carregar registros de presença.");
      return;
    }

    const dados = await resp.json();
    const itens = dados.itens;

    if (!itens || !itens.length) {
      console.warn("Nenhum item de chamada encontrado.");
      return;
    }

    const cardChamada = document.getElementById("cardChamada");

    if (!cardChamada) {
      console.error("Card de chamada não encontrado.");
      return;
    }

    cardChamada.classList.remove("d-none");

    cardChamada.innerHTML = `
      <div class="card-body">
        <h5 class="card-title mb-3">Chamada do dia ${itens[0]?.data_chamada}</h5>
        <table class="table table-hover">
          <thead>
            <tr>
              <th>Aluno</th>
              <th>Status</th>
              <th class="text-end">Ações</th>
            </tr>
          </thead>
          <tbody id="tbodyChamada"></tbody>
        </table>
      </div>
    `;

    const corpo = document.getElementById("tbodyChamada");
    corpo.innerHTML = "";

    for (const i of itens) {
      const status = i.status;
      const statusAttr = encodeURIComponent(status);

      corpo.insertAdjacentHTML(
        "beforeend",
        `
        <tr>
          <td>${i.aluno_nome}</td>
          <td>${status || "-"}</td>
          <td class="text-end">
            <button class="btn btn-sm btn-outline-success me-2"
                    onclick="abrirModalCriarChamadaItem(${chamadaId}, ${i.aluno_id})">Salvar
            </button>
            <button class="btn btn-sm btn-outline-secondary me-2"
                    onclick="abrirModalEditarChamadaItem(${chamadaId}, ${i.aluno_id}, decodeURIComponent('${statusAttr}'))">Editar
            </button>
            <button class="btn btn-sm btn-outline-danger"
                    onclick="excluirChamadaItem(${chamadaId}, ${i.aluno_id})">Excluir
            </button>
          </td>
        </tr>
      `
      );
    }
  } catch (error) {
    console.error("Erro ao listar itens da chamada:", error);
    alert("Erro de conexão com o servidor.");
  }
}

