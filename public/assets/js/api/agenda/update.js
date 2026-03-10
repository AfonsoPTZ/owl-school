let idDoHorarioAtual = null;

function editarHorario(idHorario, dia_semana, inicio, fim, disciplina) {

  idDoHorarioAtual = idHorario;

  document.getElementById("edit_dia_semana").value = dia_semana;
  document.getElementById("edit_inicio").value     = inicio;
  document.getElementById("edit_fim").value        = fim;
  document.getElementById("edit_disciplina").value = disciplina;

  const elementoModal = document.getElementById("editModalHorario");
  const modal = new bootstrap.Modal(elementoModal);
  modal.show();

}

async function salvarEdicaoHorario() {

  const dia_semana = document.getElementById("edit_dia_semana").value;
  const inicio     = document.getElementById("edit_inicio").value;
  const fim        = document.getElementById("edit_fim").value;
  const disciplina = document.getElementById("edit_disciplina").value;

  const formulario = new FormData();

  formulario.append("id", idDoHorarioAtual);
  formulario.append("dia_semana", dia_semana);
  formulario.append("inicio", inicio);
  formulario.append("fim", fim);
  formulario.append("disciplina", disciplina);

  const resposta = await fetch("/owl-school/src/api/agenda/update.php", {
    method: "POST",
    body: formulario

  });

  const resultado = await resposta.json();

  if (resultado.success) {
    alert(resultado.message);

    if (typeof carregarAgenda === "function") carregarAgenda();

    const modal = bootstrap.Modal.getInstance(document.getElementById("editModalHorario"));
    modal.hide();
  
  } else {
    alert(resultado.message);
  }
}

document.getElementById("btnSalvarHorario").addEventListener("click", salvarEdicaoHorario);