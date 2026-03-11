document.getElementById("formLogin").onsubmit = async function (e) {
  e.preventDefault();

  const email = document.getElementById("email").value.trim();
  const senha = document.getElementById("senha").value.trim();

  if (email === "" || senha === "") {
    alert("Preencha todos os campos antes de enviar.");
    return;
  }

  const formularioDados = new FormData();

  formularioDados.append("email", email);
  formularioDados.append("senha", senha);

  const resposta = await fetch("/owl-school/src/api/auth/login.php", {
    method: "POST",
    body: formularioDados,
  });

  const resultado = await resposta.json();

  if (!resultado.success) {
    alert(resultado.message || "Falha no login.");
    return;
  }

  const tipo = resultado.usuario.tipo_usuario;

  if (tipo === "aluno")            window.location.href = "/owl-school/public/aluno/aluno.html";
  else if (tipo === "professor")   window.location.href = "/owl-school/public/professor/professor.html";
  else if (tipo === "responsavel") window.location.href = "/owl-school/public/responsavel/responsavel.html";
  else if (tipo === "admin")       window.location.href = "/owl-school/public/admin/admin.html";
  else                             alert("Tipo de usuário inválido");
};
