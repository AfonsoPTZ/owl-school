async function carregarListaAlunos() {
  const resposta = await fetch("/owl-school/api/utils?action=getAlunoSelect", { 
    method: "GET",
    credentials: "include"
  });
  const dados = await resposta.json();
  if (dados.success) {
  const select = document.getElementById("aluno_id");
    select.innerHTML = "";

    dados.alunos.forEach((aluno) => {
  const option = document.createElement("option");
      option.value = aluno.aluno_id;
      option.textContent = aluno.aluno_nome;
      select.appendChild(option);

    });

  } else {
    console.error("Erro ao carregar alunos:", dados.message);
  }
}

document.addEventListener("DOMContentLoaded", carregarListaAlunos);

