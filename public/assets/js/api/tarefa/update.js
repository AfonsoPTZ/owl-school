let idDaTarefaAtual = null;


async function editarTarefa(idTarefa) {

  idDaTarefaAtual = idTarefa;

  const elementoModal = document.getElementById("editModal");
  const modal = new bootstrap.Modal(elementoModal);
  modal.show();


  const resposta = await fetch("/owl-school/src/api/tarefa/read.php", { method: "POST" });
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

  const formulario = new FormData();


  formulario.append("id", idDaTarefaAtual);
  formulario.append("titulo", titulo);
  formulario.append("descricao", descricao);
  formulario.append("data_entrega", dataEntrega);


  const resposta = await fetch("/owl-school/src/api/tarefa/update.php", {
    method: "POST",
    body: formulario
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