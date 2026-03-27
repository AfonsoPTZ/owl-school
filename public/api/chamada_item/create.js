let idChamadaAtual = null;
let idAlunoAtualChamada = null;

async function abrirModalCriarChamadaItem(chamadaId, alunoId) {
  idChamadaAtual = chamadaId;
  idAlunoAtualChamada = alunoId;

  const elementoModal = document.getElementById("createChamadaItemModal");

  if (!elementoModal) {
    console.error("Modal de criação não encontrado.");
    return;
  }

  const modal = new bootstrap.Modal(elementoModal);
  modal.show();

  document.getElementById("create_status").value = "presente";
}

async function salvarChamadaItem() {
  const status = document.getElementById("create_status").value.trim();

  if (!idChamadaAtual || !idAlunoAtualChamada || !status) {
    alert("Preencha todos os campos antes de enviar.");
    return;
  }

  const formularioDados = new FormData();
  formularioDados.append("chamadaId", idChamadaAtual);
  formularioDados.append("alunoId", idAlunoAtualChamada);
  formularioDados.append("status", status);

  try {
    const resposta = await fetch("/owl-school/api/chamada_item", {
      method: "POST",
      body: formularioDados
    });

    const resultado = await resposta.json();

    if (!resultado.success) {
      alert(resultado.message || "Erro ao criar registro de presença.");
      return;
    }

    alert(resultado.message || "Registro de presença criado com sucesso.");

    if (typeof listarItensDaChamada === "function") {
      listarItensDaChamada(idChamadaAtual);
    }

    const modal = bootstrap.Modal.getInstance(document.getElementById("createChamadaItemModal"));
    modal.hide();
  } catch (error) {
    console.error("Erro ao criar registro de presença:", error);
    alert("Erro de conexão com o servidor.");
  }
}

document.getElementById("btnSalvarChamadaItem")?.addEventListener("click", salvarChamadaItem);


