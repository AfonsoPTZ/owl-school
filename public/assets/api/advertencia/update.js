let idAdvertenciaAtual = null;

async function abrirModalEditarAdvertencia(idAdvertencia) {

    idAdvertenciaAtual = idAdvertencia;


    const elementoModal = document.getElementById("editModalAdvertencia");
    const modal = new bootstrap.Modal(elementoModal);
    modal.show();


    const resposta = await fetch("/owl-school/src/api/advertencia/read.php", { method: "POST" });

    const dados = await resposta.json();


    const advertencia = dados.advertencias.find(advertencia => String(advertencia.id) === String(idAdvertencia));


    document.getElementById("edit_titulo").value = advertencia.titulo;
    document.getElementById("edit_descricao").value = advertencia.descricao;

}

async function salvarAdvertencia() {


    const titulo = document.getElementById("edit_titulo").value;
    const descricao = document.getElementById("edit_descricao").value;


    const formularioDados = new FormData();


    formularioDados.append("id", idAdvertenciaAtual);
    formularioDados.append("titulo", titulo);
    formularioDados.append("descricao", descricao);


    const resp = await fetch("/owl-school/src/api/advertencia/update.php", {
      method: "POST",
      body: formularioDados

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