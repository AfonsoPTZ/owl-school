async function carregarProvas() {

  const response = await fetch("/owl-school/src/api/prova/read.php", { method: "POST" });
  const resultado = await response.json();


  const corpoTabela = document.getElementById("tbodyProvas");
  corpoTabela.innerHTML = "";


  for (const prova of resultado.provas) {

    corpoTabela.insertAdjacentHTML("beforeend", `
      <tr>
        <td>${prova.titulo}</td>
        <td>${prova.data}</td>
        <td class="text-end">

          <button class="btn btn-sm btn-outline-primary" onclick="listarNotasDaProva(${prova.id})">Lançar notas</button>
          <button class="btn btn-sm btn-outline-secondary" onclick="editarProva(${prova.id})">Editar</button>
          <button class="btn btn-sm btn-outline-danger" onclick="excluirProva(${prova.id})">Excluir</button>

        </td>
      </tr>
    `);
  }
}


document.addEventListener("DOMContentLoaded", carregarProvas);