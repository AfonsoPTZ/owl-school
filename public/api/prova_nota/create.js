let idProvaAtual = null;
let idAlunoAtual = null;

async function abrirModalCriarNota(provaId, alunoId) {
  idProvaAtual = provaId;
  idAlunoAtual = alunoId;

  const elementoModal = document.getElementById("createNotaModal");

  if (!elementoModal) {
    console.error("Modal de criação não encontrado.");
    return;
  }

  const modal = new bootstrap.Modal(elementoModal);
  modal.show();

  document.getElementById("create_nota").value = "";
}

async function salvarCriacaoNota() {
  let nota = document.getElementById("create_nota").value.trim();

  nota = nota.replace(",", ".");

  if (!nota) {
    alert("Preencha todos os campos antes de enviar.");
    return;
  }

  if (!idProvaAtual || !idAlunoAtual) {
    alert("Dados de prova ou aluno não informados.");
    return;
  }

  const formularioDados = new FormData();
  formularioDados.append("provaId", idProvaAtual);
  formularioDados.append("alunoId", idAlunoAtual);
  formularioDados.append("nota", nota);

  try {
    const resposta = await fetch("/owl-school/api/prova_nota", {
      method: "POST",
      body: formularioDados
    });

    const resultado = await resposta.json();

    if (!resultado.success) {
      alert(resultado.message || "Erro ao criar nota.");
      return;
    }

    alert(resultado.message || "Nota criada com sucesso.");

    if (typeof listarNotasDaProva === "function") {
      listarNotasDaProva(idProvaAtual);
    }

    const modal = bootstrap.Modal.getInstance(document.getElementById("createNotaModal"));
    modal.hide();
  } catch (error) {
    console.error("Erro ao criar nota:", error);
    alert("Erro de conexão com o servidor.");
  }
}

document.getElementById("btnSalvarNota")?.addEventListener("click", salvarCriacaoNota);


