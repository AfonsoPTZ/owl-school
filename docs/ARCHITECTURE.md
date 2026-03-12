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
