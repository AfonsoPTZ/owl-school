/**
 * Carrega nome do usuário logado
 * Requisição: GET /owl-school/api/utils?action=getName
 * Popula o elemento com id='userName'
 */
async function carregarNomeUsuario() {
  const resposta = await fetch("/owl-school/api/utils?action=getName", {
    method: "GET",
    credentials: "include"
  });

  if (!resposta.ok) {
    console.error("Erro ao carregar nome do usuário:", resposta.status);
    return;
  }

  const resultado = await resposta.json();
  const spanNome = document.getElementById("userName");

  if (!resultado.user_name) {
    spanNome.innerHTML = "Usuário";
    return;
  }

  spanNome.innerHTML = resultado.user_name;
}

document.addEventListener("DOMContentLoaded", carregarNomeUsuario);
