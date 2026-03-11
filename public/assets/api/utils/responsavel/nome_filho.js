async function carregarNomeFilho() {

    const response = await fetch("/owl-school/src/api/utils/responsavel/nome_filho.php", { method: "POST"});
    const resultado = await response.json();


    const container = document.getElementById("nomeFilho");
    container.innerHTML = "";
    container.insertAdjacentHTML("beforeend", `<span>${resultado.nome_filho}</span>`);

}

document.addEventListener("DOMContentLoaded", carregarNomeFilho);