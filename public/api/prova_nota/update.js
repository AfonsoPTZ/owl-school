let idProvaAtualEditar = null;
let idAlunoAtualEditar = null;

async function abrirModalEditarNota(provaId, alunoId, notaAtual = "") {
  idProvaAtualEditar = provaId;
  idAlunoAtualEditar = alunoId;

  const elementoModal = document.getElementById("editNotaModal");

  if (!elementoModal) {
    console.error("Modal de edição não encontrado.");
    return;
  }

  const modal = new bootstrap.Modal(elementoModal);
  modal.show();

  document.getElementById("edit_nota").value = notaAtual;
}

async function salvarEdicaoNota() {
  let nota = document.getElementById("edit_nota").value.trim();

  nota = nota.replace(",", ".");

  if (!nota) {
    alert("Preencha todos os campos antes de salvar.");
    return;
  }

  if (!idProvaAtualEditar || !idAlunoAtualEditar) {
    alert("Dados de prova ou aluno não informados.");
    return;
  }

  try {
    const resposta = await fetch("/owl-school/api/prova_nota", {
      method: "PUT",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        provaId: idProvaAtualEditar,
        alunoId: idAlunoAtualEditar,
        nota
      })
    });

    const resultado = await resposta.json();

    if (!resultado.success) {
      alert(resultado.message || "Erro ao salvar nota.");
      return;
    }

    alert(resultado.message || "Nota atualizada com sucesso.");

    if (typeof listarNotasDaProva === "function") {
      listarNotasDaProva(idProvaAtualEditar);
    }

    const modal = bootstrap.Modal.getInstance(document.getElementById("editNotaModal"));
    modal.hide();
  } catch (error) {
    console.error("Erro ao salvar nota:", error);
    alert("Erro de conexão com o servidor.");
  }
}

document.getElementById("btnSalvarEdicaoNota")?.addEventListener("click", salvarEdicaoNota);

