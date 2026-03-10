async function carregarTarefas() {

    const response = await fetch("/owl-school/src/api/tarefa/read.php", { method: "POST" });
    const resultado = await response.json();


    const tipoUsuario = resultado.tipo_usuario; 
    const corpoTabela = document.getElementById("tbodyTarefas");
    corpoTabela.innerHTML = "";

    
if (!resultado.tarefas || resultado.tarefas.length === 0) {
  corpoTabela.insertAdjacentHTML("beforeend", `
    <tr>
      <td colspan="4" class="text-center text-muted">Nenhuma Tarefa.</td>
    </tr>
  `);
  return;
}

    

    for (const tarefa of resultado.tarefas) {
      let acoesHTML = "";

      if (tipoUsuario === "professor" || tipoUsuario === "admin") {
        acoesHTML = `
          <button class="btn btn-sm btn-outline-secondary" onclick="editarTarefa(${tarefa.id})">Editar</button>
          <button class="btn btn-sm btn-outline-danger ms-1" onclick="excluirTarefa(${tarefa.id})">Excluir</button>
        `;
      }

      corpoTabela.innerHTML += `
        <tr>
          <td>${tarefa.titulo}</td>
          <td>${tarefa.data_entrega}</td>
          <td class="small">${tarefa.descricao}</td>
          <td class="text-end">${acoesHTML}</td>
        </tr>
      `;
    }

}

document.addEventListener("DOMContentLoaded", carregarTarefas);