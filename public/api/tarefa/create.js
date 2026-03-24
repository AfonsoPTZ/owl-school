async function criarTarefa() {
  const titulo        = document.getElementById("titulo").value;
  const descricao     = document.getElementById("descricao").value;
  const data_entrega  = document.getElementById("data_entrega").value;
  if (titulo === "" || descricao === "" || data_entrega === "") {
  alert("Preencha todos os campos antes de enviar.");
  return;}
  const formularioDados = new FormData();

  formularioDados.append("titulo", titulo);
  formularioDados.append("descricao", descricao);
  formularioDados.append("data_entrega", data_entrega);
  const resposta = await fetch("/owl-school/api/tarefa", {
    method: "POST",
    body: formularioDados

  });
  const resultado = await resposta.json();
  if (resultado.success) {

    alert(resultado.message);

    document.getElementById("titulo").value = "";
    document.getElementById("descricao").value = "";
    document.getElementById("data_entrega").value = "";
  if (typeof carregarTarefas === "function") {carregarTarefas();}

  } else {
    alert(resultado.message);
  }
}

document.getElementById("btnCriar").addEventListener("click", criarTarefa);


