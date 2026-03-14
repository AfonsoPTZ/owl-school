let idAdvertenciaAtual = null;
  async function abrirModalEditarAdvertencia(idAdvertencia) {

    idAdvertenciaAtual = idAdvertencia;
  const elementoModal = document.getElementById("editModalAdvertencia");
  const modal = new bootstrap.Modal(elementoModal);
    modal.show();
  const resposta = await fetch("/owl-school/app/Routes/advertencia.php", { method: "GET" });
  const dados = await resposta.json();
  const advertencia = dados.advertencias.find(advertencia => String(advertencia.id) === String(idAdvertencia));

    document.getElementById("edit_titulo").value = advertencia.titulo;
    document.getElementById("edit_descricao").value = advertencia.descricao;

}
  async function salvarAdvertencia() {
  const titulo = document.getElementById("edit_titulo").value;
  const descricao = document.getElementById("edit_descricao").value;
  const resp = await fetch("/owl-school/app/Routes/advertencia.php", {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        id: idAdvertenciaAtual,
        titulo,
        descricao
      })
    });
  const resultado = await resp.json();
  if (resultado.success) {

      alert(resultado.message);
  if (typeof carregarAdvertencias === "function") {carregarAdvertencias();}
  const modal = bootstrap.Modal.getInstance(document.getElementById("editModalAdvertencia"));
      modal.hide();

    } else {
      alert(resultado.message);
    }
}

document.getElementById("btnSalvarAdvertencia").addEventListener("click", salvarAdvertencia);

