async function carregarAdvertencias() {

    const response = await fetch("/owl-school/src/api/utils/responsavel/advertencia_filho.php", { method: "POST" });
    const resultado = await response.json();


    const corpoTabela = document.getElementById("tbodyAdvertencias");
    corpoTabela.innerHTML = "";


    if (!resultado.advertencias || resultado.advertencias.length === 0) {
      corpoTabela.insertAdjacentHTML("beforeend", `
        <tr>
          <td colspan="2" class="text-center text-muted">Nenhuma advertência.</td>
        </tr>
      `);
      return;
    }


    for (const adv of resultado.advertencias) {

      corpoTabela.insertAdjacentHTML("beforeend", `
        <tr>
          <td>${adv.titulo}</td>
          <td class="small">${adv.descricao}</td>
        </tr>
      `);
    }
}

document.addEventListener("DOMContentLoaded", carregarAdvertencias);