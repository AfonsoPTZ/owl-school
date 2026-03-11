let idChamadaAtualEditar = null;
let idAlunoAtualEditarChamada = null;


async function abrirModalEditarChamadaItem(chamadaId, alunoId, statusAtual = "presente") {

  idChamadaAtualEditar = chamadaId;
  idAlunoAtualEditarChamada = alunoId;

  const elementoModal = document.getElementById("editChamadaItemModal");
  const modal = new bootstrap.Modal(elementoModal);
  modal.show();

  document.getElementById("edit_status").value = statusAtual;
}


async function salvarEdicaoChamadaItem() {

  const status = document.getElementById("edit_status").value;

  const resposta = await fetch("/owl-school/src/api/chamada_item/index.php", {
    method: "PUT",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      chamada_id: idChamadaAtualEditar,
      aluno_id: idAlunoAtualEditarChamada,
      status
    })
  });


  const resultado = await resposta.json();


  if (resultado.success) {

    alert(resultado.message);

    if (typeof listarItensDaChamada === "function") {listarItensDaChamada(idChamadaAtualEditar);}

    const modal = bootstrap.Modal.getInstance(document.getElementById("editChamadaItemModal"));
    modal.hide();

  } else {
    alert(resultado.message);
  }
}


document.getElementById("btnSalvarEdicaoChamadaItem").addEventListener("click", salvarEdicaoChamadaItem);