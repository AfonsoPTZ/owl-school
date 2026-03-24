async function criarAdvertencia() {
  const titulo = document.getElementById("titulo").value;
  const descricao = document.getElementById("descricao").value;
  const aluno_id = document.getElementById("aluno_id").value;
  if (titulo === "" || descricao === "" || aluno_id === "") {
  alert("Preencha todos os campos antes de enviar.");
  return;}
  const formularioDados = new FormData();

  formularioDados.append("titulo", titulo);
  formularioDados.append("descricao", descricao);
  formularioDados.append("aluno_id", aluno_id);
  const resposta = await fetch("/owl-school/api/advertencia", {
    method: "POST",
    body: formularioDados

  });
  const resultado = await resposta.json();
  if (resultado.success) {

    alert(resultado.message);

    document.getElementById("titulo").value = "";
    document.getElementById("descricao").value = "";
    document.getElementById("aluno_id").selectedIndex = 0;
  if (typeof carregarAdvertencias === "function") {carregarAdvertencias();}

  } else {
    alert(resultado.message);
  }
}

document.getElementById("btnCriarAdvertencia").addEventListener("click", criarAdvertencia);

