let idDaProvaAtual = null;

async function editarProva(idProva) {
  idDaProvaAtual = idProva;

  const elementoModal = document.getElementById("editModalProva");

  if (!elementoModal) {
    console.error("Modal de edição não encontrado.");
    return;
  }

  try {
    const resposta = await fetch("/owl-school/api/prova", {
      method: "GET"
    });

    const dados = await resposta.json();
    const prova = dados.provas.find(p => String(p.id) === String(idProva));

    if (!prova) {
      alert("Prova não encontrada.");
      return;
    }

    document.getElementById("edit_titulo_prova").value = prova.titulo;
    document.getElementById("edit_data_prova").value = prova.data;

    const modal = new bootstrap.Modal(elementoModal);
    modal.show();
  } catch (error) {
    console.error("Erro ao carregar dados da prova:", error);
    alert("Erro ao carregar a prova para edição.");
  }
}

async function salvarProva() {
  const titulo = document.getElementById("edit_titulo_prova").value.trim();
  const data = document.getElementById("edit_data_prova").value;

  if (!idDaProvaAtual) {
    alert("Nenhuma prova selecionada para edição.");
    return;
  }

  if (!titulo || !data) {
    alert("Preencha todos os campos antes de salvar.");
    return;
  }

  try {
    const resposta = await fetch("/owl-school/api/prova", {
      method: "PUT",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        id: idDaProvaAtual,
        titulo,
        data
      })
    });

    const resultado = await resposta.json();

    if (!resultado.success) {
      alert(resultado.message || "Erro ao salvar prova.");
      return;
    }

    alert(resultado.message || "Prova atualizada com sucesso.");

    if (typeof carregarProvas === "function") {
      carregarProvas();
    }

    const modal = bootstrap.Modal.getInstance(document.getElementById("editModalProva"));
    modal.hide();
  } catch (error) {
    console.error("Erro ao salvar prova:", error);
    alert("Erro de conexão com o servidor.");
  }
}

document.getElementById("btnSalvarProva")?.addEventListener("click", salvarProva);


