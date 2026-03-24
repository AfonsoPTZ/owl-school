async function carregarNomeUsuario() {
  console.log("DEBUG: carregarNomeUsuario iniciado");
  const resposta = await fetch("/owl-school/api/utils?action=getName", {
    method: "GET",
    credentials: "include"
  });
  
  console.log("DEBUG: resposta.ok =", resposta.ok, "status =", resposta.status);
  
  if (!resposta.ok) {
    console.error("Erro ao carregar nome do usuário:", resposta.status);
    return;
  }
  
  const resultado = await resposta.json();
  console.log("DEBUG: resultado =", resultado);
  const spanNome = document.getElementById("userName");
  
  if (!resultado.user_name) {
    console.log("DEBUG: user_name vazio, setando Usuário");
    spanNome.innerHTML = "Usuário";
    return;
  }

  console.log("DEBUG: setando nome =", resultado.user_name);
  spanNome.innerHTML = resultado.user_name;
}

document.addEventListener("DOMContentLoaded", carregarNomeUsuario);
