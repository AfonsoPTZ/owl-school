async function carregarComunicados() {
  
    const user = await getSessionUser();

    const resposta = await fetch("/owl-school/src/api/comunicado/index.php", { method: "GET" });

    const resultado = await resposta.json();


    const tipoUsuario = user.tipo_usuario;

    const corpoTabela = document.getElementById("tbodyComunicados");
    corpoTabela.innerHTML = "";


    if (!resultado.comunicados || resultado.comunicados.length === 0) {
      corpoTabela.insertAdjacentHTML("beforeend", `
        <tr>
          <td colspan="2" class="text-center text-muted">Nenhum Comunicado.</td>
        </tr>
      `);
      return;
    }


    for (const comunicado of resultado.comunicados) {

      let acoesHTML = "";
    

      if (tipoUsuario === "professor" || tipoUsuario === "admin") {
        acoesHTML = `
          <button class="btn btn-sm btn-outline-secondary" onclick="editarComunicado(${comunicado.id})">Editar</button>
          <button class="btn btn-sm btn-outline-danger ms-1" onclick="excluirComunicado(${comunicado.id})">Excluir</button>
        `;
      }


      corpoTabela.insertAdjacentHTML("beforeend", `
        <tr>
          <td>${comunicado.titulo}</td>
          <td class="small">${comunicado.corpo}</td>
          <td class="text-end">${acoesHTML}</td>
        </tr>
      `);
    }
}


document.addEventListener("DOMContentLoaded", carregarComunicados);