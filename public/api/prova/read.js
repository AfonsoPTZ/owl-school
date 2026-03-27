async function carregarProvas() {
  const corpoTabela = document.getElementById("tbodyProvas");

  if (!corpoTabela) {
    console.error("Elemento #tbodyProvas não encontrado.");
    return;
  }

  corpoTabela.innerHTML = "";

  try {
    const user = await getSessionUser();

    if (!user) {
      alert("Falha ao carregar dados do usuário.");
      return;
    }

    const tipoUsuario = user.tipo_usuario;

    const response = await fetch("/owl-school/api/prova", { method: "GET" });

    if (!response.ok) {
      alert("Erro ao carregar provas.");
      return;
    }

    const resultado = await response.json();
    const provas = resultado.provas || [];

    if (!provas.length) {
      corpoTabela.insertAdjacentHTML(
        "beforeend",
        `
        <tr>
          <td colspan="3" class="text-center text-muted">Nenhuma prova encontrada.</td>
        </tr>
      `
      );
      return;
    }

    for (const prova of provas) {
      let acoesHTML = `
        <button class="btn btn-sm btn-outline-primary" onclick="listarNotasDaProva(${prova.id})">Lançar notas</button>
        <button class="btn btn-sm btn-outline-secondary ms-1" onclick="editarProva(${prova.id})">Editar</button>
        <button class="btn btn-sm btn-outline-danger ms-1" onclick="excluirProva(${prova.id})">Excluir</button>
      `;

      corpoTabela.insertAdjacentHTML(
        "beforeend",
        `
        <tr>
          <td>${prova.titulo}</td>
          <td>${prova.data}</td>
          <td class="text-end">${acoesHTML}</td>
        </tr>
      `
      );
    }
  } catch (error) {
    console.error("Erro ao carregar provas:", error);
    alert("Erro de conexão com o servidor.");

    corpoTabela.innerHTML = `
      <tr>
        <td colspan="3" class="text-center text-danger">Erro ao carregar provas.</td>
      </tr>
    `;
  }
}

document.addEventListener("DOMContentLoaded", carregarProvas);

