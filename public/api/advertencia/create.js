async function criarAdvertencia() {
  const titulo = document.getElementById("titulo").value.trim();
  const descricao = document.getElementById("descricao").value.trim();
  const aluno_id = document.getElementById("aluno_id").value;

  if (!titulo || !descricao || !aluno_id) {
    alert("Preencha todos os campos antes de enviar.");
    return;
  }

  const formularioDados = new FormData();
  formularioDados.append("titulo", titulo);
  formularioDados.append("descricao", descricao);
  formularioDados.append("aluno_id", aluno_id);

  try {
    const resposta = await fetch("/owl-school/api/advertencia", {
      method: "POST",
      body: formularioDados
    });

    const resultado = await resposta.json();

    if (!resultado.success) {
      alert(resultado.message || "Erro ao criar advertência.");
      return;
    }

    alert(resultado.message || "Advertência criada com sucesso.");

    document.getElementById("titulo").value = "";
    document.getElementById("descricao").value = "";
    document.getElementById("aluno_id").selectedIndex = 0;

    if (typeof carregarAdvertencias === "function") {
      carregarAdvertencias();
    }
  } catch (error) {
    console.error("Erro ao criar advertência:", error);
    alert("Erro de conexão com o servidor.");
  }
}

document.getElementById("btnCriarAdvertencia")?.addEventListener("click", criarAdvertencia);

