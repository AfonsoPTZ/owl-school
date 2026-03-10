let idChamadaAtual = null;
let idAlunoAtualChamada = null;


async function abrirModalCriarChamadaItem(chamadaId, alunoId) {

  idChamadaAtual = chamadaId;
  idAlunoAtualChamada = alunoId;

  const elementoModal = document.getElementById("createChamadaItemModal");
  const modal = new bootstrap.Modal(elementoModal);
  modal.show();

  document.getElementById("create_status").value = "presente";
}


async function salvarChamadaItem() {

  const status = document.getElementById("create_status").value;


  if (idChamadaAtual === "" || idAlunoAtualChamada === "" || status === "") {
  alert("Preencha todos os campos antes de enviar.");
  return;}


  const formularioDados = new FormData();

  formularioDados.append("chamada_id", idChamadaAtual);
  formularioDados.append("aluno_id", idAlunoAtualChamada);
  formularioDados.append("status", status);


  const resposta = await fetch("/owl-school/src/api/chamada_item/create.php", {
    method: "POST",
    body: formularioDados

  });


  const resultado = await resposta.json();


  if (resultado.success) {

    alert(resultado.message);

    if (typeof listarItensDaChamada === "function") {listarItensDaChamada(idChamadaAtual);}

    const modal = bootstrap.Modal.getInstance(document.getElementById("createChamadaItemModal"));
    modal.hide();

  } else {
    alert(resultado.message);
  }
}


document.getElementById("btnSalvarChamadaItem").addEventListener("click", salvarChamadaItem);
