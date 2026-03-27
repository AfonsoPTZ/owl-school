async function excluirProva(id) {
  if (!id) {
    alert("ID da prova não informado.");
    return;
  }

  if (!confirm("Tem certeza que deseja excluir?")) {
    return;
  }

  try {
    const resposta = await fetch("/owl-school/api/prova", {
      method: "DELETE",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({ id })
    });

    const resultado = await resposta.json();

    if (!resultado.success) {
      alert(resultado.message || "Erro ao excluir prova.");
      return;
    }

    alert(resultado.message || "Prova excluída com sucesso.");

    if (typeof carregarProvas === "function") {
      carregarProvas();
    }
  } catch (error) {
    console.error("Erro ao excluir prova:", error);
    alert("Erro de conexão com o servidor.");
  }
}


