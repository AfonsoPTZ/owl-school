let idDaTarefaAtual = null;

async function editarTarefa(idTarefa) {
  idDaTarefaAtual = idTarefa;

  const elementoModal = document.getElementById("editModal");

  if (!elementoModal) {
    console.error("Modal de edição não encontrado.");
    return;
  }

  try {
    const resposta = await fetch("/owl-school/api/tarefa", {
      method: "GET"
    });

    const dados = await resposta.json();
    const tarefas = dados.tarefas || [];
    const tarefa = tarefas.find(tarefa => String(tarefa.id) === String(idTarefa));

    if (!tarefa) {
      alert("Tarefa não encontrada.");
      return;
    }

    document.getElementById("edit_titulo").value = tarefa.titulo;
    document.getElementById("edit_descricao").value = tarefa.descricao;
    document.getElementById("edit_data").value = tarefa.data_entrega;

    const modal = new bootstrap.Modal(elementoModal);
    modal.show();
  } catch (error) {
    console.error("Erro ao carregar dados da tarefa:", error);
    alert("Erro ao carregar a tarefa para edição.");
  }
}

async function salvarTarefa() {
  const titulo = document.getElementById("edit_titulo").value.trim();
  const descricao = document.getElementById("edit_descricao").value.trim();
  const dataEntrega = document.getElementById("edit_data").value;

  if (!idDaTarefaAtual) {
    alert("Nenhuma tarefa selecionada para edição.");
    return;
  }

  if (!titulo || !descricao || !dataEntrega) {
    alert("Preencha todos os campos antes de salvar.");
    return;
  }

  try {
    const resposta = await fetch("/owl-school/api/tarefa", {
      method: "PUT",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        id: idDaTarefaAtual,
        titulo,
        descricao,
        data_entrega: dataEntrega
      })
    });

    const resultado = await resposta.json();

    if (!resultado.success) {
      alert(resultado.message || "Erro ao atualizar tarefa.");
      return;
    }

    alert(resultado.message || "Tarefa atualizada com sucesso.");

    if (typeof carregarTarefas === "function") {
      carregarTarefas();
    }

    const modalElement = document.getElementById("editModal");
    const modal = bootstrap.Modal.getInstance(modalElement);

    if (modal) {
      modal.hide();
    }

    idDaTarefaAtual = null;
  } catch (error) {
    console.error("Erro ao salvar tarefa:", error);
    alert("Erro de conexão com o servidor.");
  }
}

document.getElementById("btnSalvar")?.addEventListener("click", salvarTarefa);