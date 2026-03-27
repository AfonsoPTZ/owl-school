async function excluirComunicado(id) {
  if (!id) {
    alert("ID do comunicado não informado.");
    return;
  }

  if (!confirm("Tem certeza que deseja excluir?")) {
    return;
  }

  try {
    const resposta = await fetch("/owl-school/api/comunicado", {
      method: "DELETE",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({ id })
    });

    const resultado = await resposta.json();

    if (!resultado.success) {
      alert(resultado.message || "Erro ao excluir comunicado.");
      return;
    }

    alert(resultado.message || "Comunicado excluído com sucesso.");

    if (typeof carregarComunicados === "function") {
      carregarComunicados();
    }
  } catch (error) {
    console.error("Erro ao excluir comunicado:", error);
    alert("Erro de conexão com o servidor.");
  }
}


