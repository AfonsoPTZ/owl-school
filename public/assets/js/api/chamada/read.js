async function carregarChamadas() {


    const response = await fetch("/owl-school/src/api/chamada/read.php", { method: "POST" });
    const resultado = await response.json();


    const corpoTabela = document.getElementById("tbodyChamadas");
    corpoTabela.innerHTML = "";

  
    for (const chamada of resultado.chamadas) {

  corpoTabela.insertAdjacentHTML("beforeend", `
    <tr>
      <td>${chamada.data}</td>
      <td class="text-end">

        <button class="btn btn-primary btn-sm"
                onclick="listarItensDaChamada(${chamada.id})">
          Lançar presença
        </button>

        <button class="btn btn-sm btn-outline-secondary"
                onclick="editarChamada(${chamada.id})">
          Editar
        </button>

        <button class="btn btn-sm btn-outline-danger"
                onclick="excluirChamada(${chamada.id})">
          Excluir
        </button>
        
      </td>
    </tr>
  `);
}


}

document.addEventListener("DOMContentLoaded", carregarChamadas);