async function excluirHorario(id) {
  if (!id) return;
  if (!confirm("Tem certeza que deseja excluir este horário?")) return;
  const resposta = await fetch("/owl-school/api/agenda", {
    method: "DELETE",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id })
  });
  const resultado = await resposta.json();
  if (resultado.success) {

    alert(resultado.message);
  if (typeof carregarAgenda === "function") {carregarAgenda();}

  } else {
    alert(resultado.message);
  }
}


