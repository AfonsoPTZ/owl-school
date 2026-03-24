# 📋 Contrato dos Endpoints

Documentação do contrato (request/response) de todos os endpoints da API.

**Nota:** Com o novo router (`index.php`), todas as requisições são roteadas via URLs amigáveis:
- `/api/{recurso}/{acao}` → Controller::acao()
- `.htaccess` reescreve para `index.php`

---

## Padrão de Response

Todas as respostas seguem este formato:

```json
{
  "success": boolean,
  "message": "descrição da ação",
  "status": number,
  "data": object | null
}
```

### Status Codes Padronizados

- `201` - Criado com sucesso (POST)
- `200` - Operação bem-sucedida (GET, PUT, DELETE)
- `404` - Recurso não encontrado
- `422` - Erro de validação
- `500` - Erro do servidor

---

## 🤖 IA - Assistente Inteligente

### POST /api/ia/chat

Pergunta ao assistente com detecção inteligente de intenção.

**Request:**
```json
{
  "pergunta": "Quais são minhas tarefas?"
}
```

**Response 200:**
```json
{
  "success": true,
  "message": "Você tem 3 tarefas: Matemática (03/04), Português (05/04), História (07/04)",
  "intent": "consultar_tarefas",
  "status": 200
}
```

**Intenções suportadas:**
- `consultar_tarefas` - Busca tarefas
- `consultar_provas` - Busca provas agendadas
- `consultar_notas` - Busca notas/desempenho
- `consultar_advertencias` - Busca advertências
- `consultar_agenda` - Busca agenda/horários
- `consultar_chamada` - Busca frequência/presença
- `consultar_comunicados` - Busca comunicados
- `desconhecido` - Pergunta fora do escopo

**Response 400 (validação):**
```json
{
  "success": false,
  "message": "Pergunta obrigatória.",
  "status": 400
}
```

**Response com fallback (sem Gemini):**
```json
{
  "success": true,
  "message": "Você tem 3 tarefas:\n- Matemática\n- Português\n- História",
  "intent": "consultar_tarefas",
  "status": 200,
  "fallback": true
}
```

---

## 🔐 Autenticação

### POST /api/auth/login

**Request:**
```json
{
  "email": "joao.aluno@teste.com",
  "senha": "123456"
}
```

**Response 200:**
```json
{
  "success": true,
  "message": "Login realizado com sucesso.",
  "status": 200,
  "data": {
    "id": 1,
    "nome": "João da Silva",
    "email": "joao.aluno@teste.com",
    "tipo_usuario": "aluno"
  }
}
```

**Response 401:**
```json
{
  "success": false,
  "message": "Email ou senha incorretos.",
  "status": 401,
  "data": null
}
```

---

### GET /api/auth/logout

**Response 200:**
```json
{
  "success": true,
  "message": "Logout realizado.",
  "status": 200,
  "data": null
}
```

---

## 📚 Tarefas

### GET /owl-school/src/api/tarefa/read.php

**Response 200:**
```json
{
  "success": true,
  "message": "",
  "status": 200,
  "data": [
    { "id": 1, "titulo": "Exercício", "descricao": "...", "data_entrega": "2024-12-20", "status": "em_aberto" }
  ]
}
```

### POST /owl-school/src/api/tarefa/create.php

**Request:**
```json
{
  "titulo": "Exercício de Matemática",
  "descricao": "Resolver exercícios",
  "dataEntrega": "2024-12-20"
}
```

**Response 201:**
```json
{
  "success": true,
  "message": "Tarefa criada com sucesso.",
  "status": 201,
  "data": { "id": 42, "titulo": "...", ... }
}
```

**Response 422:**
```json
{
  "success": false,
  "message": "Erro de validação: Título é obrigatório",
  "status": 422,
  "data": null
}
```

### PUT /owl-school/src/api/tarefa/update.php

**Request:**
```json
{
  "id": 1,
  "status": "entregue"
}
```

**Response 200:**
```json
{
  "success": true,
  "message": "Tarefa atualizada com sucesso.",
  "status": 200,
  "data": null
}
```

**Response 404:**
```json
{
  "success": false,
  "message": "Tarefa não encontrada.",
  "status": 404,
  "data": null
}
```

### DELETE /owl-school/src/api/tarefa/delete.php

**Request:**
```json
{ "id": 1 }
```

**Response 200:**
```json
{
  "success": true,
  "message": "Tarefa deletada com sucesso.",
  "status": 200,
  "data": null
}
```

---

## 📋 Chamadas

### GET /owl-school/src/api/chamada/read.php
**Response 200:** Array de chamadas

### POST /owl-school/src/api/chamada/create.php
**Request:**
```json
{
  "data": "2024-12-15",
  "descricao": "Aula de Português"
}
```
**Response 201:** Chamada criada

### PUT /owl-school/src/api/chamada/update.php
**Request:**
```json
{
  "id": 1,
  "descricao": "Aula de Português"
}
```
**Response 200:** Atualizado

### DELETE /owl-school/src/api/chamada/delete.php
**Request:**
```json
{ "id": 1 }
```
**Response 200:** Deletado

---

## 👤 Itens de Chamada

### GET /owl-school/src/api/chamada_item/read.php?chamada_id=10
**Response 200:** Array de presenças

### POST /owl-school/src/api/chamada_item/create.php
**Request:**
```json
{
  "chamadaId": 10,
  "alunoId": 5,
  "status": "presente"
}
```
**Response 201:** Presença registrada

### PUT /owl-school/src/api/chamada_item/update.php
**Request:**
```json
{
  "chamadaId": 10,
  "alunoId": 5,
  "status": "falta"
}
```
**Response 200:** Atualizado

### DELETE /owl-school/src/api/chamada_item/delete.php
**Request:**
```json
{
  "chamadaId": 10,
  "alunoId": 5
}
```
**Response 200:** Deletado

---

## 📢 Comunicados

### GET /owl-school/src/api/comunicado/read.php
**Response 200:** Array de comunicados

### POST /owl-school/src/api/comunicado/create.php
**Request:**
```json
{
  "titulo": "Aula Cancelada",
  "corpo": "A aula de hoje será cancelada."
}
```
**Response 201:** Comunicado criado

### PUT /owl-school/src/api/comunicado/update.php
**Response 200:** Atualizado

### DELETE /owl-school/src/api/comunicado/delete.php
**Response 200:** Deletado

---

## 📝 Advertências

### GET /owl-school/src/api/advertencia/read.php
**Response 200:** Array de advertências

### POST /owl-school/src/api/advertencia/create.php
**Request:**
```json
{
  "aluno_id": 5,
  "descricao": "Comportamento inadequado",
  "data": "2024-12-15"
}
```
**Response 201:** Advertência criada

### PUT /owl-school/src/api/advertencia/update.php
**Response 200:** Atualizado

### DELETE /owl-school/src/api/advertencia/delete.php
**Response 200:** Deletado

---

## 📅 Agenda

### GET /owl-school/src/api/agenda/read.php
**Response 200:**
```json
{
  "success": true,
  "status": 200,
  "por_dia": {
    "segunda": [{ "id": 1, "hora": "10:00", "atividade": "Aula de Matemática" }],
    "terca": [],
    "quarta": [],
    "quinta": [],
    "sexta": []
  }
}
```

### POST /owl-school/src/api/agenda/create.php
**Request:**
```json
{
  "dia": "segunda",
  "hora": "10:00",
  "atividade": "Aula de Matemática"
}
```
**Response 201:** Evento criado

### PUT /owl-school/src/api/agenda/update.php
**Response 200:** Atualizado

### DELETE /owl-school/src/api/agenda/delete.php
**Response 200:** Deletado

---

## 🎓 Provas

### GET /owl-school/src/api/prova/read.php
**Response 200:** Array de provas

### POST /owl-school/src/api/prova/create.php
**Request:**
```json
{
  "titulo": "Prova de Matemática",
  "data": "2024-12-20",
  "descricao": "Avaliação de Frações"
}
```
**Response 201:** Prova criada

### PUT /owl-school/src/api/prova/update.php
**Response 200:** Atualizado

### DELETE /owl-school/src/api/prova/delete.php
**Response 200:** Deletado

---

## 🎯 Notas de Prova

### GET /owl-school/src/api/prova_nota/read.php?prova_id=15
**Response 200:**
```json
{
  "success": true,
  "status": 200,
  "titulo_prova": "Prova de Matemática",
  "notas": [
    { "prova_id": 15, "aluno_id": 5, "aluno_nome": "João Silva", "nota": 8.5 }
  ]
}
```

### POST /owl-school/src/api/prova_nota/create.php
**Request:**
```json
{
  "provaId": 15,
  "alunoId": 5,
  "nota": 8.5,
  "observacao": "Ótimo desempenho"
}
```
**Response 201:** Nota registrada

### PUT /owl-school/src/api/prova_nota/update.php
**Response 200:** Atualizado

### DELETE /owl-school/src/api/prova_nota/delete.php
**Request:**
```json
{
  "provaId": 15,
  "alunoId": 5
}
```
**Response 200:** Deletado

---

## 📌 Observações

- **Chaves Compostas:** CHAMADA_ITEM e PROVA_NOTA usam (id1 + id2) para identificar registro
- **Normalização:** DTOs aceitam camelCase (JSON) e snake_case (FormData)
- **Status Codes:** Sempre retornam o HTTP code apropriado
- **Validação:** Erro 422 quando dados inválidos
- **Não Encontrado:** Erro 404 quando recurso não existe
- **Erros:** Mensagem descritiva no campo `message`

---

[← Voltar](README.md)
