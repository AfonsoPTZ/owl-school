async function criarComunicado() {
  const titulo = document.getElementById("titulo").value;
  const corpo  = document.getElementById("corpo").value;
  if (titulo === "" || corpo === "") {
  alert("Preencha todos os campos antes de enviar.");
  return;}
  const formularioDados = new FormData();

  formularioDados.append("titulo", titulo);
  formularioDados.append("corpo", corpo);
  const resposta = await fetch("/owl-school/app/Routes/comunicado.php", {
    method: "POST",
    body: formularioDados

  });
  const resultado = await resposta.json();
  if (resultado.success) {

    alert(resultado.message);

    document.getElementById("titulo").value = "";
    document.getElementById("corpo").value  = "";
  if (typeof carregarComunicados === "function") {carregarComunicados();}

  } else {
    alert(resultado.message);
  }
}

document.getElementById("btnCriar").addEventListener("click", criarComunicado);


