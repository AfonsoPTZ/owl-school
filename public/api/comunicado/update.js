let idDoComunicadoAtual = null;

async function editarComunicado(idComunicado) {
  if (!idComunicado) {
    console.error("ID do comunicado não informado.");
    return;
  }

  idDoComunicadoAtual = idComunicado;

  const elementoModal = document.getElementById("editModalComunicado");

  if (!elementoModal) {
    console.error("Modal de edição não encontrado.");
    return;
  }

  try {
    const resposta = await fetch("/owl-school/api/comunicado", { method: "GET" });

    if (!resposta.ok) {
      alert("Erro ao carregar comunicado.");
      return;
    }

    const dados = await resposta.json();

    if (!dados.comunicados || !dados.comunicados.length) {
      alert("Nenhum comunicado encontrado.");
      return;
    }

    const comunicado = dados.comunicados.find((com) => String(com.id) === String(idComunicado));

    if (!comunicado) {
      alert("Comunicado não encontrado.");
      return;
    }

    document.getElementById("edit_titulo").value = comunicado.titulo || "";
    document.getElementById("edit_corpo").value = comunicado.corpo || "";

    const modal = new bootstrap.Modal(elementoModal);
    modal.show();
  } catch (error) {
    console.error("Erro ao abrir modal de edição:", error);
    alert("Erro ao carregar comunicado.");
  }
}

async function salvarComunicado() {
  const titulo = document.getElementById("edit_titulo").value.trim();
  const corpo = document.getElementById("edit_corpo").value.trim();

  if (!titulo || !corpo) {
    alert("Preencha todos os campos antes de salvar.");
    return;
  }

  if (!idDoComunicadoAtual) {
    alert("ID do comunicado não informado.");
    return;
  }

  try {
    const resposta = await fetch("/owl-school/api/comunicado", {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        id: idDoComunicadoAtual,
        titulo,
        corpo
      })
    });

    const resultado = await resposta.json();

    if (!resultado.success) {
      alert(resultado.message || "Erro ao salvar comunicado.");
      return;
    }

    alert(resultado.message || "Comunicado atualizado com sucesso.");

    if (typeof carregarComunicados === "function") {
      carregarComunicados();
    }

    const modal = bootstrap.Modal.getInstance(document.getElementById("editModalComunicado"));
    modal.hide();
  } catch (error) {
    console.error("Erro ao salvar comunicado:", error);
    alert("Erro de conexão com o servidor.");
  }
}

document.getElementById("btnSalvarComunicado")?.addEventListener("click", salvarComunicado);

