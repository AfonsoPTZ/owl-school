async function carregarNomeResponsavel() {

    const response = await fetch("/owl-school/src/api/utils/aluno/nome_responsavel.php", { method: "POST" });
    const resultado = await response.json();

    const container = document.getElementById("nomeResponsavel");
    container.innerHTML = "";
    container.insertAdjacentHTML("beforeend", `<span>${resultado.nome_responsavel}</span>`);

}

document.addEventListener("DOMContentLoaded", carregarNomeResponsavel);
