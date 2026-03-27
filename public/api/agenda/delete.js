async function excluirHorario(id) {
  if (!id) {
    alert("ID do horário não informado.");
    return;
  }

  if (!confirm("Tem certeza que deseja excluir este horário?")) {
    return;
  }

  try {
    const resposta = await fetch("/owl-school/api/agenda", {
      method: "DELETE",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({ id })
    });

    const resultado = await resposta.json();

    if (!resultado.success) {
      alert(resultado.message || "Erro ao excluir horário.");
      return;
    }

    alert(resultado.message || "Horário excluído com sucesso.");

    if (typeof carregarAgenda === "function") {
      carregarAgenda();
    }
  } catch (error) {
    console.error("Erro ao excluir horário:", error);
    alert("Erro de conexão com o servidor.");
  }
}


