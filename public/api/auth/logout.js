async function fazerLogout() {
  try {
    const resposta = await fetch("/owl-school/api/logout", {
      method: "POST",
      credentials: "include"
    });

    if (!resposta.ok) {
      alert("Erro ao sair.");
      return;
    }

    window.location.href = "/owl-school/public/index.html";
  } catch (error) {
    console.error("Erro ao fazer logout:", error);
    alert("Erro de conex\u00e3o com o servidor.");
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


