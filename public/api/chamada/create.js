async function criarChamada() {
  const data = document.getElementById("data").value;

  if (!data) {
    alert("Preencha todos os campos antes de enviar.");
    return;
  }

  const formularioDados = new FormData();
  formularioDados.append("data", data);

  try {
    const resposta = await fetch("/owl-school/api/chamada", {
      method: "POST",
      body: formularioDados
    });

    const resultado = await resposta.json();

    if (!resultado.success) {
      alert(resultado.message || "Erro ao criar chamada.");
      return;
    }

    alert(resultado.message || "Chamada criada com sucesso.");

    document.getElementById("data").value = "";

    if (typeof carregarChamadas === "function") {
      carregarChamadas();
    }
  } catch (error) {
    console.error("Erro ao criar chamada:", error);
    alert("Erro de conexão com o servidor.");
  }
}

document.getElementById("btnCriarChamada")?.addEventListener("click", criarChamada);


