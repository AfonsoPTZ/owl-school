async function criarHorario() {
  const dia_semana = document.getElementById("dia_semana").value;
  const inicio = document.getElementById("inicio").value;
  const fim = document.getElementById("fim").value;
  const disciplina = document.getElementById("disciplina").value.trim();

  if (!dia_semana || !inicio || !fim || !disciplina) {
    alert("Preencha todos os campos antes de enviar.");
    return;
  }

  const formularioDados = new FormData();
  formularioDados.append("dia_semana", dia_semana);
  formularioDados.append("inicio", inicio);
  formularioDados.append("fim", fim);
  formularioDados.append("disciplina", disciplina);

  try {
    const resposta = await fetch("/owl-school/api/agenda", {
      method: "POST",
      body: formularioDados
    });

    const resultado = await resposta.json();

    if (!resultado.success) {
      alert(resultado.message || "Erro ao criar horário.");
      return;
    }

    alert(resultado.message || "Horário criado com sucesso.");

    document.getElementById("dia_semana").value = "";
    document.getElementById("inicio").value = "";
    document.getElementById("fim").value = "";
    document.getElementById("disciplina").value = "";

    if (typeof carregarAgenda === "function") {
      carregarAgenda();
    }
  } catch (error) {
    console.error("Erro ao criar horário:", error);
    alert("Erro de conexão com o servidor.");
  }
}

document.getElementById("btnCriarHorario")?.addEventListener("click", criarHorario);

