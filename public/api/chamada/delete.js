async function excluirChamada(id) {
  if (!id) {
    alert("ID da chamada não informado.");
    return;
  }

  if (!confirm("Tem certeza que deseja excluir?")) {
    return;
  }

  try {
    const resposta = await fetch("/owl-school/api/chamada", {
      method: "DELETE",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({ id })
    });

    const resultado = await resposta.json();

    if (!resultado.success) {
      alert(resultado.message || "Erro ao excluir chamada.");
      return;
    }

    alert(resultado.message || "Chamada excluída com sucesso.");

    if (typeof carregarChamadas === "function") {
      carregarChamadas();
    }
  } catch (error) {
    console.error("Erro ao excluir chamada:", error);
    alert("Erro de conexão com o servidor.");
  }
}


