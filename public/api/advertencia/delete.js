async function excluirAdvertencia(id) {
  if (!id) return;
  if (!confirm("Tem certeza que deseja excluir?")) return;
  const resposta = await fetch("/owl-school/app/Routes/advertencia.php", {
    method: "DELETE",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id })
  });
  const resultado = await resposta.json();
  if (resultado.success) {

    alert(resultado.message);
  if (typeof carregarAdvertencias === "function") {carregarAdvertencias();}

  } else {
    alert(resultado.message);
  }
}


