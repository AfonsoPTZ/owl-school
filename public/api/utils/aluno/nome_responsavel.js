async function carregarNomeResponsavel() {
  console.log("DEBUG: carregarNomeResponsavel iniciado");
  const response = await fetch("/owl-school/api/utils_aluno?action=getNomeResponsavel", {
    method: "GET",
    credentials: "include"
  });
  
  console.log("DEBUG: response.ok =", response.ok, "status =", response.status);
  
  if (!response.ok) {
    console.error("Erro ao carregar nome do responsável:", response.status);
    return;
  }
  
  const resultado = await response.json();
  console.log("DEBUG: resultado =", resultado);
  const container = document.getElementById("nomeResponsavel");
  
  if (!resultado.success || !resultado.nome_responsavel) {
    console.log("DEBUG: nome_responsavel vazio, setando Não informado");
    container.innerHTML = "<span>Não informado</span>";
    return;
  }
  
  console.log("DEBUG: setando nome_responsavel =", resultado.nome_responsavel);
  container.innerHTML = "";
  container.insertAdjacentHTML("beforeend", `<span>${resultado.nome_responsavel}</span>`);
}

document.addEventListener("DOMContentLoaded", carregarNomeResponsavel);


