async function criarTarefa() {
  const titulo = document.getElementById("titulo").value.trim();
  const descricao = document.getElementById("descricao").value.trim();
  const dataEntrega = document.getElementById("data_entrega").value;

  if (!titulo || !descricao || !dataEntrega) {
    alert("Preencha todos os campos antes de enviar.");
    return;
  }

  const formData = new FormData();
  formData.append("titulo", titulo);
  formData.append("descricao", descricao);
  formData.append("data_entrega", dataEntrega);

  try {
    const resposta = await fetch("/owl-school/api/tarefa", {
      method: "POST",
      body: formData
    });

    const resultado = await resposta.json();

    if (!resultado.success) {
      alert(resultado.message || "Erro ao criar tarefa.");
      return;
    }

    alert(resultado.message || "Tarefa criada com sucesso.");

    document.getElementById("titulo").value = "";
    document.getElementById("descricao").value = "";
    document.getElementById("data_entrega").value = "";

    if (typeof carregarTarefas === "function") {
      carregarTarefas();
    }
  } catch (error) {
    console.error("Erro ao criar tarefa:", error);
    alert("Erro de conexão com o servidor.");
  }
}

document.getElementById("btnCriar")?.addEventListener("click", criarTarefa);