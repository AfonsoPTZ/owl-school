async function getSessionUser() {
  const response = await fetch("/owl-school/app/Routes/authme.php", {
    method: "GET",
    credentials: "include"
  });
  if (!response.ok) {
    return null;
  }
  const resultado = await response.json();
  const user = resultado.user || resultado.usuario || null;
  return user;
}
  async function protectPage(rolesPermitidas = []) {
  const user = await getSessionUser();
  if (!user) {
    window.location.href = "/owl-school/public/index.html?erro=login";
    return;
  }
  const tipo = String(user.tipo_usuario || "").trim().toLowerCase();
  if (rolesPermitidas.length && !rolesPermitidas.includes(tipo)) {
    window.location.href = "/owl-school/public/index.html?erro=permissao";
    return;
  }

  document.body.style.display = "block";
}


