async function listarItensDaChamada(chamadaId) {
  

    const formularioDados = new FormData();
    formularioDados.append("chamada_id", chamadaId);


    const resp = await fetch("/owl-school/src/api/chamada_item/read.php", {
      method: "POST",
      body: formularioDados
    });


    const dados = await resp.json();


    const itens = dados.itens; 
 

    const cardChamada = document.getElementById("cardChamada");
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

      corpo.insertAdjacentHTML("beforeend", `
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
      `);
    }

}