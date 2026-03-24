async function excluirNota(provaId, alunoId) {
  if (!confirm("Tem certeza que deseja excluir esta nota?")) return;
  const resposta = await fetch("/owl-school/api/prova_nota", {
    method: "DELETE",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ provaId: provaId, alunoId: alunoId })
  });
  const resultado = await resposta.json();
  if (resultado.success) {

    alert(resultado.message);
  if (typeof listarNotasDaProva === "function") {listarNotasDaProva(provaId);}

  } else {
    alert(resultado.message);
  }
}


