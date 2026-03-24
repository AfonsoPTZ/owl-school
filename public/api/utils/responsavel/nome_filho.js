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

