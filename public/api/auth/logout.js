async function fazerLogout() {
  const resposta = await fetch("/owl-school/app/Routes/logout.php", {
    method: "POST",
    credentials: 'include'
  });
  if (resposta.ok) {
    window.location.href = "/owl-school/public/index.html";
  } else {
    alert("Erro ao sair.");
  }
}

document.addEventListener("click", function (evento) {
  const botaoLogout = evento.target.closest("#btnLogout");
  if (!botaoLogout) {
    return;
  }

  evento.preventDefault();
  evento.stopPropagation();

  fazerLogout();

});


