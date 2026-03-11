let idDoComunicadoAtual = null;

async function editarComunicado(idComunicado) {

  idDoComunicadoAtual = idComunicado;

  const elementoModal = document.getElementById("editModalComunicado");
  const modal = new bootstrap.Modal(elementoModal);
  modal.show();


  const resposta = await fetch("/owl-school/src/api/comunicado/read.php", { method: "POST" });

  const dados = await resposta.json();

  const comunicado = dados.comunicados.find(comunicado => String(comunicado.id) === String(idComunicado));

  document.getElementById("edit_titulo").value = comunicado.titulo;
  document.getElementById("edit_corpo").value  = comunicado.corpo;
}


async function salvarComunicado() {

  const titulo = document.getElementById("edit_titulo").value;
  const corpo  = document.getElementById("edit_corpo").value;


  const formularioDados = new FormData();

  formularioDados.append("id", idDoComunicadoAtual);
  formularioDados.append("titulo", titulo);
  formularioDados.append("corpo", corpo);


  const resposta = await fetch("/owl-school/src/api/comunicado/update.php", {
    method: "POST",
    body: formularioDados

  });


  const resultado = await resposta.json();


  if (resultado.success) {

    alert("Comunicado atualizado com sucesso!");

    if (typeof carregarComunicados === "function") {carregarComunicados();}

    const modal = bootstrap.Modal.getInstance(document.getElementById("editModalComunicado"));
    modal.hide();

  } else {
    alert(resultado.message);
  }
}


document.getElementById("btnSalvarComunicado").addEventListener("click", salvarComunicado);