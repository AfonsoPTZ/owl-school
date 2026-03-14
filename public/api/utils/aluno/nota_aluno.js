async function carregarNotasAluno() {
  const response = await fetch("/owl-school/app/Routes/utils_aluno.php?action=getNotas", { method: "POST" });
  const resultado = await response.json();
  const corpoTabela = document.getElementById("tbodyNotas");
    corpoTabela.innerHTML = "";
  if (!resultado.notas || resultado.notas.length === 0) {
      corpoTabela.insertAdjacentHTML("beforeend", `
        <tr>
          <td colspan="3" class="text-center text-muted">Nenhuma nota encontrada.</td>
        </tr>
      `);
      return;
    }
  for (const item of resultado.notas) {

      corpoTabela.insertAdjacentHTML("beforeend", `
        <tr>
          <td>${item.titulo}</td>
          <td>${item.data}</td>
          <td>${item.nota}</td>
        </tr>
      `);
    }
}

document.addEventListener("DOMContentLoaded", carregarNotasAluno);

