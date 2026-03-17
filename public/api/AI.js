async function enviarPerguntaIA() {
  const input = document.getElementById("inputIA");
  const respostaBox = document.getElementById("respostaIA");

  const pergunta = input.value.trim();
  if (!pergunta) return;

  respostaBox.innerText = "Pensando...";

  try {
    const response = await fetch("/owl-school/app/Routes/ia.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({ pergunta })
    });

    const resultado = await response.json();

    console.log("Intent detectada:", resultado.intent);
    respostaBox.innerText = resultado.message || "Sem resposta.";
  } catch (error) {
    console.error(error);
    respostaBox.innerText = "Erro ao enviar pergunta.";
  }

  input.value = "";
}