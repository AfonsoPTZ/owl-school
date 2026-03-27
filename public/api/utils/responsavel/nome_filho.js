/**
 * Carrega nome do filho (para responsável)
 * Requisição: GET /owl-school/api/utils_responsavel?action=getNomeFilho
 * Popula elemento com id='nomeFilho'
 */
async function carregarNomeFilho() {
  const response = await fetch("/owl-school/api/utils_responsavel?action=getNomeFilho", { 
    method: "GET",
    credentials: "include"
  });
  const resultado = await response.json();
  const container = document.getElementById("nomeFilho");
    container.innerHTML = "";
    container.insertAdjacentHTML("beforeend", `<span>${resultado.nome_filho}</span>`);

}

document.addEventListener("DOMContentLoaded", carregarNomeFilho);

