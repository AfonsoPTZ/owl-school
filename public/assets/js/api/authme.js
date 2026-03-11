async function getSessionUser() {
  const response = await fetch("/owl-school/src/api/authme.php", {
    method: "GET",
    credentials: "include"
  });

  if (!response.ok) {
    return null;
  }

  const resultado = await response.json();
  return resultado.user;
}


async function protectPage(rolesPermitidas = []) {

  const user = await getSessionUser();

  if (!user) {
    window.location.href = "/owl-school/public/index.php?erro=login";
    return;
  }

  if (rolesPermitidas.length && !rolesPermitidas.includes(user.tipo_usuario)) {
    window.location.href = "/owl-school/public/index.php?erro=permissao";
    return;
  }

  document.body.style.display = "block";
}