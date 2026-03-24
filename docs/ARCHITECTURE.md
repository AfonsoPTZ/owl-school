# 🏗️ Arquitetura

## Fluxo Simple

```
Requisição HTTP
     ↓
Controller (recebe, valida)
     ↓
Service (lógica)
     ↓
Repository (banco)
     ↓
Resposta JSON
```

## Componentes

### Controller
Recebe a requisição, valida com DTO e chama Service.

```php
public function create(): void {
  try {
    $dados = $this->getDadosRequest();
    $dto = new TarefaDTO($dados);
    $resultado = $this->service->create($dto);
    $this->json($resultado, $resultado['status']);
  } catch (\Throwable $e) {
    $this->handleException($e, 'create');
  }
}
```

### 2️⃣ **Controller** (`/src/controllers/`)

Coordena a requisição. Responsável por:
- Receber dados da requisição (JSON ou FormData)
- Validar entrada
- Chamar Service
- Retornar resposta HTTP com status code correto

**Padrão:**
```php
class TarefaController extends BaseController {
  private TarefaService $service;

  public function __construct($conn) {
    parent::__construct($conn);
    $this->service = new TarefaService($conn);
  }

  public function create(): void {
    try {
      // 1. Parse data (JSON ou FormData)
      $dados = $this->getDadosRequest();
      
      // 2. Create DTO
      $dto = new TarefaDTO($dados);
      
      // 3. Call Service
      $resultado = $this->service->create($dto);
      
      // 4. Return response
      $this->json($resultado, $resultado['status']);
    } catch (\Throwable $e) {
      $this->handleException($e, 'create');
    }
  }
}
```

**Métodos base (do BaseController):**
- `json(array $data, int $statusCode)` - Retorna JSON com status
- `handleException(\Throwable $e, string $action)` - Trata erros

---

### Service
Contém lógica de negócio. Valida com Validator e chama Repository.

```php
public function create(TarefaDTO $dto): array {
  $validator = new TarefaValidator($dto);
  if (!$validator->validateCreate()) {
    return ['success' => false, 'message' => $validator->getErrors()[0], 'status' => 422];
  }
  
  if ($this->repository->create($dto)) {
    return ['success' => true, 'message' => 'Criado.', 'status' => 201];
  }
  return ['success' => false, 'message' => 'Erro.', 'status' => 500];
}
```

### Repository
Acessa banco de dados. Usa prepared statements.

```php
public function create(TarefaDTO $dto): bool {
  $sql = "INSERT INTO tarefa (titulo, descricao) VALUES (?, ?)";
  $stmt = $this->conn->prepare($sql);
  if (!$stmt) return false;
  
  $stmt->bind_param("ss", $dto->titulo, $dto->descricao);
  $executou = $stmt->execute();
  if (!$executou) { $stmt->close(); return false; }
  
  $stmt->close();
  return true;
}
```

### DTO
Normaliza dados de JSON ou FormData.

```php
class TarefaDTO {
  public string $titulo;
  public string $descricao;
  
  public function __construct(array $dados = []) {
    $this->titulo = (string) ($dados['titulo'] ?? '');
    $this->descricao = (string) ($dados['descricao'] ?? '');
  }
}
```

### Validator
Valida regras de negócio.

```php
class TarefaValidator {
  private TarefaDTO $dto;
  private array $errors = [];
  
  public function __construct(TarefaDTO $dto) { $this->dto = $dto; }
  
  public function validateCreate(): bool {
    if (empty($this->dto->titulo)) 
      $this->errors[] = 'Título obrigatório';
    return empty($this->errors);
  }
  
  public function getErrors(): array { return $this->errors; }
}
```



## Status Codes

| Code | Uso |
|------|-----|
| 200 | Sucesso |
| 201 | Criado |
| 404 | Não encontrado |
| 422 | Erro de validação |
| 500 | Erro do servidor |

---

## 🤖 Pipeline de IA (Gemini + Fallback)

### Arquitetura

```
Pergunta do usuário
     ↓
IntentDetector (Gemini → keywords fallback)
     ↓
AIService orquestra:
  ├── Busca dados (Repository)
  ├── Detecta follow-up (ContextManager)
  └── Gera resposta (PromptBuilder → Gemini)
     ↓
AnswerFormatter (fallback se Gemini falhar)
     ↓
Resposta personalizada (por papel: aluno/responsável/professor)
```

### Componentes

**1. IntentDetector** - Classifica intenção da pergunta
```php
// Tenta Gemini, fallback por keywords
$intentResult = $intentDetector->detect("Quais são minhas tarefas?");
// {intent: 'consultar_tarefas', materia: null, periodo: null}
```

**2. AIService** - Orquestra toda a pipeline
```php
// Chat com contexto e follow-ups
$resultado = $aiService->chat(new AIDTO(['pergunta' => 'Quais são?']));
// Reutiliza dados anteriores se for follow-up
```

**3. AnswerFormatter** - Formata em texto legível
```php
// Sem Gemini, retorna fallback formatado
$resposta = $formatter->fallback($intentData, $dados, $userContext);
// "Você tem 3 tarefas: Matemática, Português, ..."
```

**4. PromptBuilder** - Constrói prompts otimizados
```php
// Adapta system instruction por papel
$payload = $promptBuilder->buildAnswerPayload(
  pergunta: "Minhas tarefas?",
  intentData: $intentResult,
  dados: $dados,
  userContext: $userContext  // role: 'aluno'/'responsavel'
);
```

**5. ContextManager** - Gerencia contexto de conversa
```php
// Salva estado para follow-ups
$contextManager->saveConversationContext(
  intent: 'consultar_tarefas',
  data: $tarefas,
  response: 'Você tem 3 tarefas...'
);

// Próxima pergunta reutiliza dados
$lastIntent = $contextManager->getConversationContext()['last_intent'];
```

### Intenções Suportadas

```
consultar_tarefas    → Busca tarefas
consultar_provas     → Busca provas agendadas
consultar_notas      → Busca notas (filtro por papel)
consultar_advertencias → Busca advertências
consultar_agenda     → Busca horários de aulas
consultar_chamada    → Busca frequência/presença
consultar_comunicados → Busca avisos gerais
```

### Exemplo: Aluno Pergunta "Qual é a próxima aula?"

```
1. IntentDetector detecta: "consultar_agenda"

2. AIService busca:
   - getAgenda() → lista de aulas do week

3. PromptBuilder constrói:
   - systemInstruction: "Você é assistente escolar"
   - contextData: pergunta + dados + role=aluno
   - temperatura: 0.2 (consistência)

4. Gemini responde adaptado:
   "Sua próxima aula é Matemática às 10:00 na segunda-feira"

5. ContextManager salva:
   last_intent = 'consultar_agenda'
   last_data = [...aulas...]
   last_response = "Sua próxima aula é..."

6. Follow-up "E depois?" reutiliza dados cached
```

---

## 🚀 Roteamento via index.php

### Como Funciona

**.htaccess** reescreve todas as requisições para `index.php`:

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [QSA,L]
```

**index.php** roteia para Controller/Action:

```php
// Extrai path e method
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// /api/tarafas/create → TarefaController::create()
// /api/provas/list → ProvaController::index()
// /api/ia/chat → AIController::index()

// Busca o controller certo e executa
$controller->$action();
```

### Formato de Rota

```
/api/{recurso}/{acao}

/api/tarefas/create    →  TarefaController::create()  (POST)
/api/tarefas/list      →  TarefaController::index()   (GET)
/api/tarefas/update    →  TarefaController::update()  (PUT)
/api/tarefas/delete    →  TarefaController::delete()  (DELETE)

/api/ia/chat           →  AIController::index()       (POST)
/api/auth/login        →  AuthController::login()     (POST)
```

### Vantagens

✅ URLs amigáveis (sem .php)
✅ Roteamento centralizado
✅ Fácil adicionar middlewares
✅ Sem múltiplos arquivos na raiz
✅ Permite restrições por role/resource

---

## 📊 PDO Integration

Todas as queries usam **Prepared Statements** com **PDO**:

```php
$sql = "SELECT * FROM tarefa WHERE user_id = ?";
$stmt = $this->conn->prepare($sql);
$stmt->execute([$userId]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
```

**Benefícios:**
- Proteção contra SQL Injection
- Parâmetros tipificados
- Melhor performance (planos de execução cachados)
- Suporte a múltiplos bancos de dados
