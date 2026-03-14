async function criarProva() {
  const titulo = document.getElementById("titulo").value;
  const data   = document.getElementById("data").value;
  if (titulo === "" || data === "") {
  alert("Preencha todos os campos antes de enviar.");
  return;}
  const formularioDados = new FormData();

  formularioDados.append("titulo", titulo);
  formularioDados.append("data", data);
  const resposta = await fetch("/owl-school/app/Routes/prova.php", {
    method: "POST",
    body: formularioDados

  });
  const resultado = await resposta.json();
  if (resultado.success) {

    alert(resultado.message);

    document.getElementById("titulo").value = "";
    document.getElementById("data").value   = "";
  if (typeof carregarProvas === "function") {carregarProvas();}

  } else {
    alert(resultado.message);
  }
}

document.getElementById("btnCriar").addEventListener("click", criarProva);


