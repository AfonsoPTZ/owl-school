async function excluirAdvertencia(id) {
  if (!id) {
    alert("ID da advertência não informado.");
    return;
  }

  if (!confirm("Tem certeza que deseja excluir?")) {
    return;
  }

  try {
    const resposta = await fetch("/owl-school/api/advertencia", {
      method: "DELETE",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({ id })
    });

    const resultado = await resposta.json();

    if (!resultado.success) {
      alert(resultado.message || "Erro ao excluir advertência.");
      return;
    }

    alert(resultado.message || "Advertência excluída com sucesso.");

    if (typeof carregarAdvertencias === "function") {
      carregarAdvertencias();
    }
  } catch (error) {
    console.error("Erro ao excluir advertência:", error);
    alert("Erro de conexão com o servidor.");
  }
}


