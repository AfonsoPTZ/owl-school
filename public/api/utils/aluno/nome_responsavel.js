/**
 * Carrega nome do responsável do aluno
 * Requisição: GET /owl-school/api/utils_aluno?action=getNomeResponsavel
 * Popula elemento com id='nomeResponsavel'
 */
async function carregarNomeResponsavel() {
  const response = await fetch("/owl-school/api/utils_aluno?action=getNomeResponsavel", {
    method: "GET",
    credentials: "include"
  });

  if (!response.ok) {
    console.error("Erro ao carregar nome do responsável:", response.status);
    return;
  }

  const resultado = await response.json();
  const container = document.getElementById("nomeResponsavel");

  if (!resultado.success || !resultado.nome_responsavel) {
    container.innerHTML = "<span>Não informado</span>";
    return;
  }

  container.innerHTML = "";
  container.insertAdjacentHTML("beforeend", `<span>${resultado.nome_responsavel}</span>`);
}

document.addEventListener("DOMContentLoaded", carregarNomeResponsavel);


