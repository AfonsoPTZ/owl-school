# ✨ Destaques do Projeto

## 🏗️ Arquitetura Escalável

**Padrão de camadas** bem definido:
- Controller → Service → Repository → Database
- Cada camada tem responsabilidade única
- Fácil de testar, manter e expandir

## 🎯 DTOs Inteligentes

**Data Transfer Objects normalizam:**
- JSON (camelCase)
- FormData (snake_case)
- Conversão automática de tipos

## ✅ Validação Robusta

**Validator em cada módulo:**
- validateCreate() - para inserção
- validateUpdate() - para edição
- validateDelete() - para remoção
- Regras de negócio centralizadas

## 🔒 Segurança

- **Prepared Statements** em todas as queries (sem SQL injection)
- **Input Validation** obrigatória antes de qualquer operação
- **Authentication Middleware** para proteger rotas
- **Error Handling** centralizado

## 📊 Logging Centralizado

Sistema de logs em `src/utils/Logger.php`:
- Log de operações
- Rastreamento de erros
- Arquivo em `/logs/app.log`

```php
Logger::info("Ação realizada");
Logger::error("Erro ao processar");
Logger::warning("Aviso importante");
```

## ⚙️ Configuração com .env

Variáveis sensíveis protegidas:
- Credenciais do banco
- URLs da aplicação
- Modo debug
- Sem expor dados no código

## 🎁 BaseController

Classe base que todos controladores herdam:
- Método `json()` para resposta padronizada
- Método `handleException()` para erros
- Desacoplamento de lógica HTTP

## 📦 Prepared Statements

Defesa contra SQL Injection:
```php
$stmt = $conn->prepare("SELECT * FROM user WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
```

## 📡 API Padronizada

Todos os endpoints retornam mesmo formato:
```json
{
  "success": boolean,
  "message": "descrição",
  "status": 200,
  "data": { }
}
```

## 🔄 Response Consistente

Services sempre retornam:
- `success` - boolean
- `message` - descrição
- `status` - HTTP code (201, 200, 404, 422, 500)
- `data` - dados da operação

## 🚀 9 Módulos CRUD Completos

Todos com mesma estrutura padrão:
- AUTH, TAREFA, CHAMADA, COMUNICADO
- ADVERTENCIA, AGENDA, PROVA
- CHAMADA_ITEM, PROVA_NOTA

## 💾 Banco de Dados Normalizado

- Schema bem definido
- Relacionamentos corretos
- Seed com dados de teste

## 🤖 Assistente IA com Gemini

**Pipeline inteligente em camadas:**
- **IntentDetector**: Detecta intenção via Gemini (com fallback por keywords)
- **AIService**: Orquestra busca de dados + geração de resposta
- **AnswerFormatter**: Formata resposta em texto legível
- **PromptBuilder**: Constrói prompts otimizados para Gemini
- **ContextManager**: Mantém contexto de conversa (follow-ups)

**Suporta intenções:**
- Consultar tarefas, provas, notas, advertências
- Consultar agenda/horários, frequência
- Consultar comunicados gerais

**Adaptação por papel:**
- Aluno: "você tem..."
- Responsável: "seu filho tem..."
- Professor: "a turma tem..."

---

## 🚀 API Router Centralizado

**Roteamento moderno via `index.php`:**
- `.htaccess` reescreve arquivos estáticos e rotas para `index.php`
- URL amigável: `/api/tarefas/create` ao invés de `/src/api/tarefas/create.php`
- Sem múltiplos arquivos PHP na raiz

**Rotas suportadas:**
```
POST   /api/tarefas/create         → TarefaController::create()
GET    /api/tarefas/list           → TarefaController::index()
PUT    /api/tarefas/update         → TarefaController::update()
DELETE /api/tarefas/delete         → TarefaController::delete()

POST   /api/auth/login             → AuthController::login()
GET    /api/auth/logout            → AuthController::logout()

POST   /api/ia/chat                → AIController::index()
```
- Migrations organizadas

## 🛠️ Fácil de Expandir

Novo módulo = 7 arquivos seguindo o padrão:
1. Model (2-3 min)
2. DTO (2-3 min)
3. Validator (5-10 min)
4. Repository (5-10 min)
5. Service (5-10 min)
6. Controller (5-10 min)
7. API Files (2-3 min)

---

**Resultado:** Sistema profissional, seguro e escalável com padrões claros. 👍
