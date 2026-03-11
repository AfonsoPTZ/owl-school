async function carregarFrequencias() {

    const response = await fetch("/owl-school/src/api/utils/aluno/index.php?action=getFrequencias", { method: "POST" });
    const resultado = await response.json();


    const corpoTabela = document.getElementById("tbodyFrequencias");
    corpoTabela.innerHTML = "";


    if (!resultado.frequencias || resultado.frequencias.length === 0) {
      corpoTabela.insertAdjacentHTML("beforeend", `
        <tr>
          <td colspan="2" class="text-center text-muted">Nenhum registro de frequência.</td>
        </tr>
      `);
      return;
    }


    for (const item of resultado.frequencias) {

      corpoTabela.insertAdjacentHTML("beforeend", `
        <tr>
          <td>${item.data}</td>
          <td class="text-capitalize">${item.status}</td>
        </tr>
      `);
    }
}

document.addEventListener("DOMContentLoaded", carregarFrequencias);