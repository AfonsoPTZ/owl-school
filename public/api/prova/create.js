async function criarProva() {
  const titulo = document.getElementById("titulo").value.trim();
  const data = document.getElementById("data").value;

  if (!titulo || !data) {
    alert("Preencha todos os campos antes de enviar.");
    return;
  }

  const formularioDados = new FormData();
  formularioDados.append("titulo", titulo);
  formularioDados.append("data", data);

  try {
    const resposta = await fetch("/owl-school/api/prova", {
      method: "POST",
      body: formularioDados
    });

    const resultado = await resposta.json();

    if (!resultado.success) {
      alert(resultado.message || "Erro ao criar prova.");
      return;
    }

    alert(resultado.message || "Prova criada com sucesso.");

    document.getElementById("titulo").value = "";
    document.getElementById("data").value = "";

    if (typeof carregarProvas === "function") {
      carregarProvas();
    }
  } catch (error) {
    console.error("Erro ao criar prova:", error);
    alert("Erro de conexão com o servidor.");
  }
}

document.getElementById("btnCriar")?.addEventListener("click", criarProva);


