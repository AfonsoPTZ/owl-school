let idProvaAtual = null;
let idAlunoAtual = null;


async function abrirModalCriarNota(provaId, alunoId) {

  idProvaAtual = provaId;
  idAlunoAtual = alunoId;

  const elementoModal = document.getElementById("createNotaModal");
  const modal = new bootstrap.Modal(elementoModal);
  modal.show();

  document.getElementById("create_nota").value = "";
}


async function salvarCriacaoNota() {

  let nota = document.getElementById("create_nota").value;

  nota = nota.replace(",", ".");


  if (nota === "") {
  alert("Preencha todos os campos antes de enviar.");
  return;}


  const formularioDados = new FormData();

  formularioDados.append("prova_id", idProvaAtual);
  formularioDados.append("aluno_id", idAlunoAtual);
  formularioDados.append("nota", nota);


  const resposta = await fetch("/owl-school/src/api/prova_nota/index.php", {
    method: "POST",
    body: formularioDados

  });


  const resultado = await resposta.json();


  if (resultado.success) {

    alert(resultado.message);

    if (typeof listarNotasDaProva === "function") {listarNotasDaProva(idProvaAtual);}

    const modal = bootstrap.Modal.getInstance(document.getElementById("createNotaModal"));
    modal.hide();

  } else {
    alert(resultado.message);
  }
}


document.getElementById("btnSalvarNota").addEventListener("click", salvarCriacaoNota);
