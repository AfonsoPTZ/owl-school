async function excluirNota(provaId, alunoId) {
  if (!provaId || !alunoId) {
    alert("ID da nota não informado.");
    return;
  }

  if (!confirm("Tem certeza que deseja excluir esta nota?")) {
    return;
  }

  try {
    const resposta = await fetch("/owl-school/api/prova_nota", {
      method: "DELETE",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({ provaId, alunoId })
    });

    const resultado = await resposta.json();

    if (!resultado.success) {
      alert(resultado.message || "Erro ao excluir nota.");
      return;
    }

    alert(resultado.message || "Nota excluída com sucesso.");

    if (typeof listarNotasDaProva === "function") {
      listarNotasDaProva(provaId);
    }
  } catch (error) {
    console.error("Erro ao excluir nota:", error);
    alert("Erro de conexão com o servidor.");
  }
}


