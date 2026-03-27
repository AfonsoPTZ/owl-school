async function criarComunicado() {
  const titulo = document.getElementById("titulo").value.trim();
  const corpo = document.getElementById("corpo").value.trim();

  if (!titulo || !corpo) {
    alert("Preencha todos os campos antes de enviar.");
    return;
  }

  const formularioDados = new FormData();
  formularioDados.append("titulo", titulo);
  formularioDados.append("corpo", corpo);

  try {
    const resposta = await fetch("/owl-school/api/comunicado", {
      method: "POST",
      body: formularioDados
    });

    const resultado = await resposta.json();

    if (!resultado.success) {
      alert(resultado.message || "Erro ao criar comunicado.");
      return;
    }

    alert(resultado.message || "Comunicado criado com sucesso.");

    document.getElementById("titulo").value = "";
    document.getElementById("corpo").value = "";

    if (typeof carregarComunicados === "function") {
      carregarComunicados();
    }
  } catch (error) {
    console.error("Erro ao criar comunicado:", error);
    alert("Erro de conexão com o servidor.");
  }
}

document.getElementById("btnCriar")?.addEventListener("click", criarComunicado);


