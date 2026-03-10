async function excluirNota(provaId, alunoId) {

  if (!confirm("Tem certeza que deseja excluir esta nota?")) return;

  const formularioDados = new FormData();

  formularioDados.append("prova_id", provaId);
  formularioDados.append("aluno_id", alunoId);

  const resposta = await fetch("/owl-school/src/api/prova_nota/delete.php", {
    method: "POST",
    body: formularioDados

  });

  const resultado = await resposta.json();

  if (resultado.success) {

    alert(resultado.message);

    if (typeof listarNotasDaProva === "function") {listarNotasDaProva(provaId);}

  } else {
    alert(resultado.message);
  }
}
