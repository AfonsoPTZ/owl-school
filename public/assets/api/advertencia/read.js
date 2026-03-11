async function carregarAdvertencias() {

    const user = await getSessionUser();
    const tipoUsuario = user.tipo_usuario;

    const response = await fetch("/owl-school/src/api/advertencia/read.php", { method: "POST" });
    const resultado = await response.json();


    const corpoTabela = document.getElementById("tbodyAdvertencias");
    corpoTabela.innerHTML = "";


    if (!resultado.advertencias || resultado.advertencias.length === 0) {
      corpoTabela.insertAdjacentHTML("beforeend", `
        <tr>
          <td colspan="2" class="text-center text-muted">Nenhuma advertência encontrada.</td>
        </tr>
      `);
      return;
    }


    for (const advertencia of resultado.advertencias) {
      corpoTabela.insertAdjacentHTML("beforeend", `
        <tr>
          <td>
            <strong>${advertencia.titulo}</strong><br>
            <small class="text-muted">${advertencia.descricao}</small>
          </td>

          <td>${advertencia.aluno_nome}</td>
          <td class="text-end">

            <button class="btn btn-sm btn-outline-secondary"
              onclick="abrirModalEditarAdvertencia(${advertencia.id})">
              Editar
            </button>

            <button class="btn btn-sm btn-outline-danger"
              onclick="excluirAdvertencia(${advertencia.id})">
              Excluir
            </button>
            
          </td>
        </tr>
      `);
    }
}


document.addEventListener("DOMContentLoaded", carregarAdvertencias);