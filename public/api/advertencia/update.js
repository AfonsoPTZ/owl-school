let idAdvertenciaAtual = null;

async function abrirModalEditarAdvertencia(idAdvertencia) {
  if (!idAdvertencia) {
    console.error("ID da advertência não informado.");
    return;
  }

  idAdvertenciaAtual = idAdvertencia;

  const elementoModal = document.getElementById("editModalAdvertencia");

  if (!elementoModal) {
    console.error("Modal de edição não encontrado.");
    return;
  }

  try {
    const resposta = await fetch("/owl-school/api/advertencia", {
      method: "GET"
    });

    const dados = await resposta.json();

    if (!dados.advertencias || !dados.advertencias.length) {
      alert("Nenhuma advertência encontrada.");
      return;
    }

    const advertencia = dados.advertencias.find(
      (adv) => String(adv.id) === String(idAdvertencia)
    );

    if (!advertencia) {
      alert("Advertência não encontrada.");
      return;
    }

    document.getElementById("edit_titulo").value = advertencia.titulo || "";
    document.getElementById("edit_descricao").value = advertencia.descricao || "";

    const modal = new bootstrap.Modal(elementoModal);
    modal.show();
  } catch (error) {
    console.error("Erro ao abrir modal de edição:", error);
    alert("Erro ao carregar advertência.");
  }
}

async function salvarAdvertencia() {
  const titulo = document.getElementById("edit_titulo").value.trim();
  const descricao = document.getElementById("edit_descricao").value.trim();

  if (!titulo || !descricao) {
    alert("Preencha todos os campos antes de salvar.");
    return;
  }

  if (!idAdvertenciaAtual) {
    alert("ID da advertência não informado.");
    return;
  }

  try {
    const resposta = await fetch("/owl-school/api/advertencia", {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        id: idAdvertenciaAtual,
        titulo,
        descricao
      })
    });

    const resultado = await resposta.json();

    if (!resultado.success) {
      alert(resultado.message || "Erro ao salvar advertência.");
      return;
    }

    alert(resultado.message || "Advertência atualizada com sucesso.");

    if (typeof carregarAdvertencias === "function") {
      carregarAdvertencias();
    }

    const modal = bootstrap.Modal.getInstance(document.getElementById("editModalAdvertencia"));
    modal.hide();
  } catch (error) {
    console.error("Erro ao salvar advertência:", error);
    alert("Erro de conexão com o servidor.");
  }
}

document.getElementById("btnSalvarAdvertencia")?.addEventListener("click", salvarAdvertencia);

