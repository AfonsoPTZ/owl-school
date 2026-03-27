let idChamadaAtualEditar = null;
let idAlunoAtualEditarChamada = null;

async function abrirModalEditarChamadaItem(chamadaId, alunoId, statusAtual = "presente") {
  idChamadaAtualEditar = chamadaId;
  idAlunoAtualEditarChamada = alunoId;

  const elementoModal = document.getElementById("editChamadaItemModal");

  if (!elementoModal) {
    console.error("Modal de edição não encontrado.");
    return;
  }

  const modal = new bootstrap.Modal(elementoModal);
  modal.show();

  document.getElementById("edit_status").value = statusAtual;
}

async function salvarEdicaoChamadaItem() {
  const status = document.getElementById("edit_status").value.trim();

  if (!idChamadaAtualEditar || !idAlunoAtualEditarChamada || !status) {
    alert("Preencha todos os campos antes de salvar.");
    return;
  }

  try {
    const resposta = await fetch("/owl-school/api/chamada_item", {
      method: "PUT",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        chamadaId: idChamadaAtualEditar,
        alunoId: idAlunoAtualEditarChamada,
        status
      })
    });

    const resultado = await resposta.json();

    if (!resultado.success) {
      alert(resultado.message || "Erro ao salvar registro de presença.");
      return;
    }

    alert(resultado.message || "Registro de presença atualizado com sucesso.");

    if (typeof listarItensDaChamada === "function") {
      listarItensDaChamada(idChamadaAtualEditar);
    }

    const modal = bootstrap.Modal.getInstance(document.getElementById("editChamadaItemModal"));
    modal.hide();
  } catch (error) {
    console.error("Erro ao salvar registro de presença:", error);
    alert("Erro de conexão com o servidor.");
  }
}

document.getElementById("btnSalvarEdicaoChamadaItem")?.addEventListener("click", salvarEdicaoChamadaItem);

