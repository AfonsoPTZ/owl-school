async function excluirHorario(id) {


  if (!id) return;

  if (!confirm("Tem certeza que deseja excluir este horário?")) return;


  const formularioDados = new FormData();

  formularioDados.append("id", id);


  const resposta = await fetch("/owl-school/src/api/agenda/delete.php", {
    method: "POST",
    body: formularioDados

  });


  const resultado = await resposta.json();


  if (resultado.success) {

    alert(resultado.message);

    if (typeof carregarAgenda === "function") {carregarAgenda();}

  } else {
    alert(resultado.message);
  }
}
