async function excluirTarefa(identificador) {

  if (!identificador) return;

  if (!confirm("Tem certeza que deseja excluir?")) return;

  const formularioDados = new FormData();

  formularioDados.append("id", identificador);

  const resposta = await fetch("/owl-school/src/api/tarefa/delete.php", {
    method: "POST",
    body: formularioDados

  });

  const resultado = await resposta.json();

  if (resultado.success) {

    alert(resultado.message);

    if (typeof carregarTarefas === "function") {carregarTarefas();}

  } else {
    alert(resultado.message);
  }
}
