async function excluirProva(id) {

  if (!id) return;

  if (!confirm("Tem certeza que deseja excluir?")) return;

  const resposta = await fetch("/owl-school/src/api/prova/index.php", {
    method: "DELETE",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id })
  });

  const resultado = await resposta.json();

  if (resultado.success) {

    alert(resultado.message);

    location.reload();  

  } else {
    alert(resultado.message);
  }
}
