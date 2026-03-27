async function carregarTarefas() {
  const corpoTabela = document.getElementById("tbodyTarefas");

  if (!corpoTabela) {
    console.error("Elemento #tbodyTarefas não encontrado.");
    return;
  }

  corpoTabela.innerHTML = "";

  try {
    const user = await getSessionUser();
    const response = await fetch("/owl-school/api/tarefa", {
      method: "GET"
    });

    if (!response.ok) {
      alert("Erro ao carregar tarefas.");
      return;
    }

    const resultado = await response.json();
    const tarefas = resultado.tarefas || [];
    const tipoUsuario = user?.tipo_usuario;

    if (tarefas.length === 0) {
      corpoTabela.insertAdjacentHTML("beforeend", `
        <tr>
          <td colspan="4" class="text-center text-muted">Nenhuma tarefa.</td>
        </tr>
      `);
      return;
    }

    for (const tarefa of tarefas) {
      let acoesHTML = "";

      if (tipoUsuario === "professor" || tipoUsuario === "admin") {
        acoesHTML = `
          <button class="btn btn-sm btn-outline-secondary" onclick="editarTarefa(${tarefa.id})">Editar</button>
          <button class="btn btn-sm btn-outline-danger ms-1" onclick="excluirTarefa(${tarefa.id})">Excluir</button>
        `;
      }

      corpoTabela.insertAdjacentHTML("beforeend", `
        <tr>
          <td>${tarefa.titulo}</td>
          <td>${tarefa.data_entrega}</td>
          <td class="small">${tarefa.descricao}</td>
          <td class="text-end">${acoesHTML}</td>
        </tr>
      `);
    }
  } catch (error) {
    console.error("Erro ao carregar tarefas:", error);
    alert("Erro de conexão com o servidor.");

    corpoTabela.innerHTML = `
      <tr>
        <td colspan="4" class="text-center text-danger">
          Erro ao carregar tarefas.
        </td>
      </tr>
    `;
  }
}

document.addEventListener("DOMContentLoaded", carregarTarefas);