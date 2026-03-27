async function carregarComunicados() {
  const corpoTabela = document.getElementById("tbodyComunicados");

  if (!corpoTabela) {
    console.error("Elemento #tbodyComunicados não encontrado.");
    return;
  }

  corpoTabela.innerHTML = "";

  try {
    const user = await getSessionUser();

    if (!user) {
      alert("Falha ao carregar dados do usuário.");
      return;
    }

    const resposta = await fetch("/owl-school/api/comunicado", { method: "GET" });

    if (!resposta.ok) {
      alert("Erro ao carregar comunicados.");
      return;
    }

    const resultado = await resposta.json();
    const tipoUsuario = user.tipo_usuario;

    if (!resultado.comunicados || resultado.comunicados.length === 0) {
      corpoTabela.insertAdjacentHTML(
        "beforeend",
        `
        <tr>
          <td colspan="3" class="text-center text-muted">Nenhum Comunicado.</td>
        </tr>
      `
      );
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

      corpoTabela.insertAdjacentHTML(
        "beforeend",
        `
        <tr>
          <td>${comunicado.titulo}</td>
          <td class="small">${comunicado.corpo}</td>
          <td class="text-end">${acoesHTML}</td>
        </tr>
      `
      );
    }
  } catch (error) {
    console.error("Erro ao carregar comunicados:", error);
    alert("Erro de conexão com o servidor.");

    corpoTabela.innerHTML = `
      <tr>
        <td colspan="3" class="text-center text-danger">Erro ao carregar comunicados.</td>
      </tr>
    `;
  }
}

document.addEventListener("DOMContentLoaded", carregarComunicados);

