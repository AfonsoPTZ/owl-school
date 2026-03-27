async function excluirTarefa(id) {
  if (!id) {
    alert("ID da tarefa não informado.");
    return;
  }

  if (!confirm("Tem certeza que deseja excluir?")) {
    return;
  }

  try {
    const resposta = await fetch("/owl-school/api/tarefa", {
      method: "DELETE",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({ id })
    });

    const resultado = await resposta.json();

    if (!resultado.success) {
      alert(resultado.message || "Erro ao excluir tarefa.");
      return;
    }

    alert(resultado.message || "Tarefa excluída com sucesso.");

    if (typeof carregarTarefas === "function") {
      carregarTarefas();
    }
  } catch (error) {
    console.error("Erro ao excluir tarefa:", error);
    alert("Erro de conexão com o servidor.");
  }
}