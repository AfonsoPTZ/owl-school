async function excluirChamadaItem(chamadaId, alunoId) {
  if (!chamadaId || !alunoId) {
    alert("ID da presença não informado.");
    return;
  }

  if (!confirm("Tem certeza que deseja excluir este registro de presença?")) {
    return;
  }

  try {
    const resposta = await fetch("/owl-school/api/chamada_item", {
      method: "DELETE",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({ chamadaId, alunoId })
    });

    const resultado = await resposta.json();

    if (!resultado.success) {
      alert(resultado.message || "Erro ao excluir registro de presença.");
      return;
    }

    alert(resultado.message || "Registro de presença excluído com sucesso.");

    if (typeof listarItensDaChamada === "function") {
      listarItensDaChamada(chamadaId);
    }
  } catch (error) {
    console.error("Erro ao excluir registro de presença:", error);
    alert("Erro de conexão com o servidor.");
  }
}


