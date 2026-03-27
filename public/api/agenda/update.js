let idDoHorarioAtual = null;

function editarHorario(idHorario, dia_semana, inicio, fim, disciplina) {
  idDoHorarioAtual = idHorario;

  document.getElementById("edit_dia_semana").value = dia_semana;
  document.getElementById("edit_inicio").value = inicio;
  document.getElementById("edit_fim").value = fim;
  document.getElementById("edit_disciplina").value = disciplina;

  const elementoModal = document.getElementById("editModalHorario");

  if (!elementoModal) {
    console.error("Modal de edição não encontrado.");
    return;
  }

  const modal = new bootstrap.Modal(elementoModal);
  modal.show();
}

async function salvarEdicaoHorario() {
  const dia_semana = document.getElementById("edit_dia_semana").value.trim();
  const inicio = document.getElementById("edit_inicio").value.trim();
  const fim = document.getElementById("edit_fim").value.trim();
  const disciplina = document.getElementById("edit_disciplina").value.trim();

  if (!dia_semana || !inicio || !fim || !disciplina) {
    alert("Preencha todos os campos antes de salvar.");
    return;
  }

  if (!idDoHorarioAtual) {
    alert("ID do horário não informado.");
    return;
  }

  try {
    const resposta = await fetch("/owl-school/api/agenda", {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        id: idDoHorarioAtual,
        dia_semana,
        inicio,
        fim,
        disciplina
      })
    });

    const resultado = await resposta.json();

    if (!resultado.success) {
      alert(resultado.message || "Erro ao salvar horário.");
      return;
    }

    alert(resultado.message || "Horário atualizado com sucesso.");

    if (typeof carregarAgenda === "function") {
      carregarAgenda();
    }

    const modal = bootstrap.Modal.getInstance(document.getElementById("editModalHorario"));
    modal.hide();
  } catch (error) {
    console.error("Erro ao salvar horário:", error);
    alert("Erro de conexão com o servidor.");
  }
}

document.getElementById("btnSalvarHorario")?.addEventListener("click", salvarEdicaoHorario);

