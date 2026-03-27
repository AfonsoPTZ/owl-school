async function getSessionUser() {
  try {
    const response = await fetch("/owl-school/api/authme", {
      method: "GET",
      credentials: "include"
    });

    if (!response.ok) {
      console.error("Resposta não OK ao buscar sessão:", response.status);
      return null;
    }

    const resultado = await response.json();
    const user = resultado.user || resultado.usuario || null;

    return user;
  } catch (error) {
    console.error("Erro ao buscar sessão do usuário:", error);
    return null;
  }
}

async function protectPage(rolesPermitidas = []) {
  try {
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
  } catch (error) {
    console.error("Erro ao proteger página:", error);
    window.location.href = "/owl-school/public/index.html?erro=erro";
  }
}


