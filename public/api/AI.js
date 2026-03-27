/**
 * Envia pergunta para o assistente IA
 * Requisição: POST /owl-school/api/ia
 * Popula elemento com id='respostaIA' com a resposta
 */
async function enviarPerguntaIA() {
  const input = document.getElementById("inputIA");
  const respostaBox = document.getElementById("respostaIA");

  const pergunta = input.value.trim();
  
  if (!pergunta) {
    respostaBox.innerText = "Digite uma pergunta!";
    return;
  }

  respostaBox.innerText = "🤖 Pensando...";
  input.disabled = true;

  try {
    const payload = JSON.stringify({ pergunta });
    
    const response = await fetch("/owl-school/api/ia", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: payload
    });

    const resultado = await response.json();

    if (!resultado.success) {
      respostaBox.innerText = "❌ " + (resultado.message || "Erro ao processar.");
      return;
    }

    respostaBox.innerText = resultado.message || "Sem resposta.";
  } catch (error) {
    console.error("❌ Erro ao enviar pergunta IA:", error);
    respostaBox.innerText = "Erro ao enviar pergunta: " + error.message;
  } finally {
    input.disabled = false;
    input.value = "";
  }
}