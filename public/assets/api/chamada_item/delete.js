async function excluirChamadaItem(chamadaId, alunoId) {

  if (!confirm("Tem certeza que deseja excluir este registro de presença?")) return;

  const formularioDados = new FormData();

  formularioDados.append("chamada_id", chamadaId);
  formularioDados.append("aluno_id", alunoId);

  const resposta = await fetch("/owl-school/src/api/chamada_item/delete.php", {
    method: "POST",
    body: formularioDados

  });

  const resultado = await resposta.json();

  if (resultado.success) {

    alert(resultado.message);

    if (typeof listarItensDaChamada === "function") {listarItensDaChamada(chamadaId);}

  } else {
    alert(resultado.message);
  }
}
