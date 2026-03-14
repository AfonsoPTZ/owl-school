async function excluirChamadaItem(chamadaId, alunoId) {
  if (!confirm("Tem certeza que deseja excluir este registro de presença?")) return;
  const resposta = await fetch("/owl-school/app/Routes/chamada_item.php", {
    method: "DELETE",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ chamadaId: chamadaId, alunoId: alunoId })
  });
  const resultado = await resposta.json();
  if (resultado.success) {

    alert(resultado.message);
  if (typeof listarItensDaChamada === "function") {listarItensDaChamada(chamadaId);}

  } else {
    alert(resultado.message);
  }
}


