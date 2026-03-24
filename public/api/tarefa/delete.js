async function excluirTarefa(identificador) {
  if (!identificador) return;
  if (!confirm("Tem certeza que deseja excluir?")) return;
  const resposta = await fetch("/owl-school/api/tarefa", {
    method: "DELETE",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id: identificador })

  });
  const resultado = await resposta.json();
  if (resultado.success) {

    alert(resultado.message);
  if (typeof carregarTarefas === "function") {carregarTarefas();}

  } else {
    alert(resultado.message);
  }
}


