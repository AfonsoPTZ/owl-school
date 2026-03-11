async function excluirNota(provaId, alunoId) {

  if (!confirm("Tem certeza que deseja excluir esta nota?")) return;

  const resposta = await fetch("/owl-school/src/api/prova_nota/index.php", {
    method: "DELETE",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ prova_id: provaId, aluno_id: alunoId })
  });

  const resultado = await resposta.json();

  if (resultado.success) {

    alert(resultado.message);

    if (typeof listarNotasDaProva === "function") {listarNotasDaProva(provaId);}

  } else {
    alert(resultado.message);
  }
}
