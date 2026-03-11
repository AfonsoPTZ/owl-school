let idDaProvaAtual = null;


async function editarProva(idProva) {

  idDaProvaAtual = idProva;


  const elementoModal = document.getElementById("editModalProva");
  const modal = new bootstrap.Modal(elementoModal);
  modal.show();


  const resposta = await fetch("/owl-school/src/api/prova/read.php", { method: "POST" });

  const dados = await resposta.json();

  const prova = dados.provas.find(prova => String(prova.id) === String(idProva));


  document.getElementById("edit_titulo_prova").value = prova.titulo;
  document.getElementById("edit_data_prova").value   = prova.data;
}


async function salvarProva() {

  const titulo = document.getElementById("edit_titulo_prova").value;
  const data   = document.getElementById("edit_data_prova").value;


  const formularioDados = new FormData();

  formularioDados.append("id", idDaProvaAtual);
  formularioDados.append("titulo", titulo);
  formularioDados.append("data", data);


  const resposta = await fetch("/owl-school/src/api/prova/update.php", {
    method: "POST",
    body: formularioDados

  });


  const resultado = await resposta.json();


  if (resultado.success) {

    alert(resultado.message);

    if (typeof carregarProvas === "function") {carregarProvas();}

    const modal = bootstrap.Modal.getInstance(document.getElementById("editModalProva"));
    modal.hide();

  } else {
    alert(resultado.message);
  }
}


document.getElementById("btnSalvarProva").addEventListener("click", salvarProva);
