async function carregarAgenda() {

  const user = await getSessionUser();
  const tipoUsuario = user.tipo_usuario;

  const response = await fetch("/owl-school/src/api/agenda/read.php", { method: "POST" });
  const resultado = await response.json();

  const dias = ["segunda", "terca", "quarta", "quinta", "sexta"];
  const dados = resultado.por_dia;


  for (const dia of dias) {

    const corpo = document.getElementById(dia);
    if (!corpo) continue;

    const lista = dados[dia];
    corpo.innerHTML = "";


    if (!lista.length) {
      corpo.insertAdjacentHTML("beforeend", `
        <tr><td colspan="4" class="text-muted">Sem horários.</td></tr>
      `);
      continue;
    }


    for (const h of lista) {
      let acoesHTML = "";


      if (tipoUsuario === "professor" || tipoUsuario === "admin") {
        acoesHTML = `
          <button class="btn btn-sm btn-outline-secondary"
                  onclick="editarHorario(${h.id}, '${h.dia_semana}', '${h.inicio}', '${h.fim}', '${h.disciplina}')">
            Editar
          </button>

          <button class="btn btn-sm btn-outline-danger"
                  onclick="excluirHorario(${h.id})">
            Excluir
          </button>
        `;
      }

      corpo.insertAdjacentHTML("beforeend", `
        <tr>
          <td>${h.inicio}</td>
          <td>${h.fim}</td>
          <td>${h.disciplina}</td>
          <td class="text-end">${acoesHTML}</td>
        </tr>
      `);
    }
  }
}

document.addEventListener("DOMContentLoaded", carregarAgenda);