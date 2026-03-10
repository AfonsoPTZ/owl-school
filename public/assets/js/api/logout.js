async function fazerLogout() {
  
    const resposta = await fetch("/owl-school/src/api/logout.php", {
      method: "POST",
    });

    if (resposta.ok) {
      window.location.href = "/owl-school/public/index.php";
    } else {
      alert("Erro ao sair.");
    }
}

document.getElementById("btnLogout")?.addEventListener("click", fazerLogout);