async function excluirComunicado(id) {

  if (!id) return;

  if (!confirm("Tem certeza que deseja excluir?")) return;

  const formularioDados = new FormData();

  formularioDados.append("id", id);

  const resposta = await fetch("/owl-school/src/api/comunicado/delete.php", {
    method: "POST",
    body: formularioDados

  });

  const resultado = await resposta.json();

  if (resultado.success) {

    alert(resultado.message);

    if (typeof carregarComunicados === "function") {carregarComunicados();}

  } else {
    alert(resultado.message);
  }
}
