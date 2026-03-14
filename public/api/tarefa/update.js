let idDaTarefaAtual = null;
  async function editarTarefa(idTarefa) {

  idDaTarefaAtual = idTarefa;
  const elementoModal = document.getElementById("editModal");
  const modal = new bootstrap.Modal(elementoModal);
  modal.show();
  const resposta = await fetch("/owl-school/app/Routes/tarefa.php", { method: "GET" });
  const dados = await resposta.json();
  const tarefa = dados.tarefas.find(tarefa => String(tarefa.id) === String(idTarefa));

  document.getElementById("edit_titulo").value = tarefa.titulo;
  document.getElementById("edit_descricao").value = tarefa.descricao;
  document.getElementById("edit_data").value = tarefa.data_entrega;

}
  async function salvarTarefa() {
  const titulo = document.getElementById("edit_titulo").value;
  const descricao = document.getElementById("edit_descricao").value;
  const dataEntrega = document.getElementById("edit_data").value;
  const resposta = await fetch("/owl-school/app/Routes/tarefa.php", {
    method: "PUT",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      id: idDaTarefaAtual,
      titulo: titulo,
      descricao: descricao,
      data_entrega: dataEntrega
    })
  });
  const resultado = await resposta.json();
  if (resultado.success) {

    alert(resultado.message);
  if (typeof carregarTarefas === "function") carregarTarefas();
  const modal = bootstrap.Modal.getInstance(document.getElementById("editModal"));
    modal.hide();

  } else {
    alert(resultado.message);
  }
}

document.getElementById("btnSalvar").addEventListener("click", salvarTarefa);

