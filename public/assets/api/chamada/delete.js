async function excluirChamada(id) {

  if (!id) return;

  if (!confirm("Tem certeza que deseja excluir?")) return;

  const formularioDados = new FormData();

  formularioDados.append("id", id);

  const resposta = await fetch("/owl-school/src/api/chamada/delete.php", {
    method: "POST",
    body: formularioDados

  });

  const resultado = await resposta.json();

  if (resultado.success) {

    alert(resultado.message);

    location.reload(); 
    
  } else {
    alert(resultado.message);
  }
}
