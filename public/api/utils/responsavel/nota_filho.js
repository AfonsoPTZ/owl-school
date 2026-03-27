/**
 * Carrega notas do filho (para responsável)
 * Requisição: GET /owl-school/api/utils_responsavel?action=getNotasFilho
 * Popula tabela com id='tbodyNotas'
 */
async function carregarNotasFilhos() {
  const response = await fetch("/owl-school/api/utils_responsavel?action=getNotasFilho", { 
    method: "GET",
    credentials: "include"
  });
  const resultado = await response.json();
  const corpoTabela = document.getElementById("tbodyNotas");
    corpoTabela.innerHTML = "";
  if (!resultado.notas || resultado.notas.length === 0) {
      corpoTabela.insertAdjacentHTML("beforeend", `
        <tr>
          <td colspan="4" class="text-center text-muted">Nenhuma nota encontrada.</td>
        </tr>
      `);
      return;
    }
  for (const item of resultado.notas) {

      corpoTabela.insertAdjacentHTML("beforeend", `
        <tr>
          <td>${item.aluno_nome}</td>
          <td>${item.titulo}</td>
          <td>${item.data}</td>
          <td>${item.nota}</td>
        </tr>
      `);
    }
}

document.addEventListener("DOMContentLoaded", carregarNotasFilhos);

