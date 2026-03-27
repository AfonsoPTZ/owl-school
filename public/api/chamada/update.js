let idDaChamadaAtual = null;

async function editarChamada(idChamada) {
  if (!idChamada) {
    console.error("ID da chamada não informado.");
    return;
  }

  idDaChamadaAtual = idChamada;

  const elementoModal = document.getElementById("editModalChamada");

  if (!elementoModal) {
    console.error("Modal de edição não encontrado.");
    return;
  }

  try {
    const resposta = await fetch("/owl-school/api/chamada", {
      method: "GET"
    });

    const dados = await resposta.json();

    if (!dados.chamadas || !dados.chamadas.length) {
      alert("Nenhuma chamada encontrada.");
      return;
    }

    const chamada = dados.chamadas.find((ch) => String(ch.id) === String(idChamada));

    if (!chamada) {
      alert("Chamada não encontrada.");
      return;
    }

    document.getElementById("edit_data_chamada").value = chamada.data || "";

    const modal = new bootstrap.Modal(elementoModal);
    modal.show();
  } catch (error) {
    console.error("Erro ao abrir modal de edição:", error);
    alert("Erro ao carregar chamada.");
  }
}

async function salvarChamada() {
  const data = document.getElementById("edit_data_chamada").value.trim();

  if (!data) {
    alert("Preencha todos os campos antes de salvar.");
    return;
  }

  if (!idDaChamadaAtual) {
    alert("ID da chamada não informado.");
    return;
  }

  try {
    const resposta = await fetch("/owl-school/api/chamada", {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        id: idDaChamadaAtual,
        data
      })
    });

    const resultado = await resposta.json();

    if (!resultado.success) {
      alert(resultado.message || "Erro ao salvar chamada.");
      return;
    }

    alert(resultado.message || "Chamada atualizada com sucesso.");

    if (typeof carregarChamadas === "function") {
      carregarChamadas();
    }

    const modal = bootstrap.Modal.getInstance(document.getElementById("editModalChamada"));
    modal.hide();
  } catch (error) {
    console.error("Erro ao salvar chamada:", error);
    alert("Erro de conexão com o servidor.");
  }
}

document.getElementById("btnSalvarChamada")?.addEventListener("click", salvarChamada);


