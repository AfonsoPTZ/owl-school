let idDoComunicadoAtual = null;
  async function editarComunicado(idComunicado) {

  idDoComunicadoAtual = idComunicado;
  const elementoModal = document.getElementById("editModalComunicado");
  const modal = new bootstrap.Modal(elementoModal);
  modal.show();
  const resposta = await fetch("/owl-school/app/Routes/comunicado.php", { method: "GET" });
  const dados = await resposta.json();
  const comunicado = dados.comunicados.find(comunicado => String(comunicado.id) === String(idComunicado));

  document.getElementById("edit_titulo").value = comunicado.titulo;
  document.getElementById("edit_corpo").value  = comunicado.corpo;
}
  async function salvarComunicado() {
  const titulo = document.getElementById("edit_titulo").value;
  const corpo  = document.getElementById("edit_corpo").value;
  const resposta = await fetch("/owl-school/app/Routes/comunicado.php", {
    method: "PUT",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      id: idDoComunicadoAtual,
      titulo,
      corpo
    })
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

