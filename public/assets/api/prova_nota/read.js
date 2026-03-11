async function listarNotasDaProva(provaId) {

    const resposta = await fetch("/owl-school/src/api/prova_nota/index.php?prova_id=" + provaId, {
      method: "GET"
    });

    const dados = await resposta.json();


    const cardNotas = document.getElementById("cardNotas");
    cardNotas.classList.remove("d-none");

    cardNotas.innerHTML = `
      <div class="card-body">
        <h5 class="card-title mb-3">${dados.titulo_prova}</h5>
        <table class="table table-hover">

          <thead>
            <tr>
              <th>Aluno</th>
              <th>Nota</th>
              <th class="text-end">Ações</th>
            </tr>
          </thead>

          <tbody id="tbodyNotas"></tbody>
        </table>
      </div>
    `;

    const corpo = document.getElementById("tbodyNotas");
    corpo.innerHTML = "";


    const notas = dados.notas;


    if (!notas.length) {
      corpo.innerHTML = `<tr><td colspan="3" class="text-muted">Nenhum aluno encontrado.</td></tr>`;
      return;
    }

    
    for (const n of notas) {

      const nome = n.aluno_nome;
      const nota = n.nota ?? "-";
    
      corpo.insertAdjacentHTML("beforeend", `
        <tr>
          <td>${nome}</td>
          <td>${nota}</td>
          <td class="text-end">

            <button class="btn btn-sm btn-outline-success me-2" onclick="abrirModalCriarNota(${provaId}, ${n.aluno_id})">Salvar</button>
            <button class="btn btn-sm btn-outline-secondary me-2" onclick="abrirModalEditarNota(${provaId}, ${n.aluno_id}, '${n.nota}')">Editar</button>
            <button class="btn btn-sm btn-outline-danger" onclick="excluirNota(${provaId}, ${n.aluno_id})">Excluir</button>

          </td>
        </tr>
      `);
    }    
}