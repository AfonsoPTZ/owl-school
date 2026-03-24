async function enviarPerguntaIA() {
  const input = document.getElementById("inputIA");
  const respostaBox = document.getElementById("respostaIA");

  const pergunta = input.value.trim();
  if (!pergunta) return;

  respostaBox.innerText = "🤖 Pensando...";
  input.disabled = true;

  try {
    const response = await fetch("/owl-school/api/ia", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({ pergunta })
    });

    const resultado = await response.json();

    if (!resultado.success) {
      respostaBox.innerText = resultado.message || "Erro ao processar.";
      return;
    }

    respostaBox.innerText = resultado.message || "Sem resposta.";
  } catch (error) {
    console.error(error);
    respostaBox.innerText = "Erro ao enviar pergunta.";
  } finally {
    input.disabled = false;
    input.value = "";
  }
}