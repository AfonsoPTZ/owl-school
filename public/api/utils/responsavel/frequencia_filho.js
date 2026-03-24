async function carregarFrequenciasFilhos() {
  const response = await fetch("/owl-school/api/utils_responsavel?action=getFrequenciasFilho", { 
    method: "GET",
    credentials: "include"
  });
  const resultado = await response.json();
  const corpoTabela = document.getElementById("tbodyFrequencias");
    corpoTabela.innerHTML = "";
  if (!resultado.frequencias || resultado.frequencias.length === 0) {
      corpoTabela.insertAdjacentHTML("beforeend", `
        <tr>
          <td colspan="3" class="text-center text-muted">Nenhum registro de frequência encontrado.</td>
        </tr>
      `);
      return;
    }
  for (const item of resultado.frequencias) {

      corpoTabela.insertAdjacentHTML("beforeend", `
        <tr>
          <td>${item.aluno_nome}</td>
          <td>${item.data}</td>
          <td class="text-capitalize">${item.status}</td>
        </tr>
      `);
    }
}

document.addEventListener("DOMContentLoaded", carregarFrequenciasFilhos);

