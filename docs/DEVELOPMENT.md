# 🏗️ Guia de Desenvolvimento - OWL School

## 📚 Arquitetura Geral

```
Fluxo de Requisição:
┌─────────────────┐
│  Navegador/App  │
└────────┬────────┘
         │ HTTP Request
         ▼
┌─────────────────────────┐
│ API File (index.php)    │  ← Recebe requisição HTTP
└────────┬────────────────┘
         │
         ▼
┌─────────────────────────┐
│ Middleware              │  ← Valida autenticação
│ (AuthMiddleware)        │
└────────┬────────────────┘
         │
         ▼
┌─────────────────────────┐
│ Controller              │  ← Orquestra fluxo
│ (ex: TarefaController)  │
└────────┬────────────────┘
         │
         ▼
┌─────────────────────────┐
│ DTO (Data Transfer)     │  ← Encapsula dados
│ (ex: TarefaDTO)         │
└────────┬────────────────┘
         │
         ▼
┌─────────────────────────┐
│ Validator               │  ← Valida negócio
│ (ex: TarefaValidator)   │
└────────┬────────────────┘
         │
         ▼
┌─────────────────────────┐
│ Service                 │  ← Lógica de negócio
│ (ex: TarefaService)     │
└────────┬────────────────┘
         │
         ▼
┌─────────────────────────┐
│ Repository              │  ← Acesso a dados
│ (ex: TarefaRepository)  │
└────────┬────────────────┘
         │
         ▼
┌─────────────────────────┐
│ Database                │  ← Dados persistidos
└─────────────────────────┘
```

## 🔧 Estrutura de um Módulo

### 1. **API File** (`src/api/module/index.php`)

Ponto de entrada HTTP. Roteia requisições para o controller.

```php
<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../db/conexao.php';

use App\Controllers\TarefaController;
use App\Middleware\AuthMiddleware;

header('Content-Type: application/json');
AuthMiddleware::requireLogin();

$controller = new TarefaController($conn);
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $controller->index();
        break;
    case 'POST':
        $controller->create();
        break;
    case 'PUT':
        $input = json_decode(file_get_contents("php://input"), true);
        $_POST = is_array($input) ? $input : [];
        $controller->update();
        break;
    case 'DELETE':
        $input = json_decode(file_get_contents("php://input"), true);
        $_POST = is_array($input) ? $input : [];
        $controller->delete();
        break;
    default:
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'message' => 'Método não permitido.'
        ]);
}
```

### 2. **DTO** (`src/dtos/TarefaDTO.php`)

Encapsula dados recebidos e normaliza chaves (snake_case → camelCase).

```php
<?php
namespace App\DTOs;

class TarefaDTO
{
    public int $id;
    public string $titulo;
    public string $descricao;
    public string $dataEntrega;

    public function __construct(array $dados)
    {
        // Aceita ambos formatos (FormData usa snake_case, JSON usa camelCase)
        $this->id = (int) ($dados['id'] ?? $dados['id'] ?? 0);
        $this->titulo = trim($dados['titulo'] ?? '');
        $this->descricao = trim($dados['descricao'] ?? '');
        $this->dataEntrega = $dados['data_entrega'] ?? $dados['dataEntrega'] ?? '';
    }
}
```

### 3. **Validator** (`src/validators/TarefaValidator.php`)

Valida regras de negócio. Sempre aceita DTO.

```php
<?php
namespace App\Validators;

use App\DTOs\TarefaDTO;

class TarefaValidator
{
    private TarefaDTO $dto;

    public function __construct(TarefaDTO $dto)
    {
        $this->dto = $dto;
    }

    public function validateCreate(): array
    {
        if (empty(trim($this->dto->titulo)) || empty(trim($this->dto->dataEntrega))) {
            return [
                'success' => false,
                'message' => 'Título e data são obrigatórios.',
                'status' => 422
            ];
        }

        if (!$this->validateDate($this->dto->dataEntrega)) {
            return [
                'success' => false,
                'message' => 'Data deve estar no formato YYYY-MM-DD.',
                'status' => 422
            ];
        }

        return ['success' => true];
    }

    public function validateUpdate(): array
    {
        return $this->validateCreate();
    }

    public function validateDelete(): array
    {
        return ['success' => true];
    }

    private function validateDate(string $date): bool
    {
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) === 1;
    }
}
```

### 4. **Service** (`src/services/TarefaService.php`)

Orquestra validação e repositório.

```php
<?php
namespace App\Services;

use App\DTOs\TarefaDTO;
use App\Models\Tarefa;
use App\Repositories\TarefaRepository;
use App\Validators\TarefaValidator;

class TarefaService
{
    private TarefaRepository $repository;

    public function __construct($conn)
    {
        $this->repository = new TarefaRepository($conn);
    }

    public function create(TarefaDTO $dto): array
    {
        $validator = new TarefaValidator($dto);
        $validacao = $validator->validateCreate();

        if (!$validacao['success']) {
            return $validacao;
        }

        $tarefa = new Tarefa($dto->titulo, $dto->descricao, $dto->dataEntrega);
        $criou = $this->repository->create($tarefa);

        if (!$criou) {
            return [
                'success' => false,
                'message' => 'Erro ao criar tarefa.',
                'status' => 500
            ];
        }

        return [
            'success' => true,
            'message' => 'Tarefa criada com sucesso.',
            'status' => 201
        ];
    }

    public function update(TarefaDTO $dto): array
    {
        $validator = new TarefaValidator($dto);
        $validacao = $validator->validateUpdate();

        if (!$validacao['success']) {
            return $validacao;
        }

        $tarefa = new Tarefa($dto->titulo, $dto->descricao, $dto->dataEntrega, $dto->id);
        $atualizou = $this->repository->update($tarefa);

        if (!$atualizou) {
            return [
                'success' => false,
                'message' => 'Tarefa não encontrada.',
                'status' => 404
            ];
        }

        return [
            'success' => true,
            'message' => 'Tarefa atualizada com sucesso.',
            'status' => 200
        ];
    }

    public function delete(TarefaDTO $dto): array
    {
        $deletou = $this->repository->delete((int) $dto->id);

        if (!$deletou) {
            return [
                'success' => false,
                'message' => 'Tarefa não encontrada.',
                'status' => 404
            ];
        }

        return [
            'success' => true,
            'message' => 'Tarefa deletada com sucesso.',
            'status' => 200
        ];
    }

    public function findAll(): array
    {
        $tarefas = $this->repository->findAll();

        return [
            'success' => true,
            'tarefas' => $tarefas,
            'status' => 200
        ];
    }
}
```

### 5. **Repository** (`src/repositories/TarefaRepository.php`)

Acesso a dados. Sempre verifica erros com `if (!$stmt)`.

```php
<?php
namespace App\Repositories;

use App\Models\Tarefa;

class TarefaRepository
{
    private \mysqli $conn;

    public function __construct(\mysqli $conn)
    {
        $this->conn = $conn;
    }

    public function create(Tarefa $tarefa): bool
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO tarefa (titulo, descricao, data_entrega) VALUES (?, ?, ?)"
        );

        if (!$stmt) return false;

        $stmt->bind_param(
            "sss",
            $tarefa->titulo,
            $tarefa->descricao,
            $tarefa->dataEntrega
        );

        $executou = $stmt->execute();

        if ($executou && $stmt->affected_rows > 0) {
            $tarefa->id = $this->conn->insert_id;
            $stmt->close();
            return true;
        }

        $stmt->close();
        return false;
    }

    public function findAll(): array
    {
        $stmt = $this->conn->prepare(
            "SELECT id, titulo, descricao, data_entrega FROM tarefa ORDER BY id DESC"
        );

        if (!$stmt) return [];

        $stmt->execute();
        $resultado = $stmt->get_result();
        $tarefas = [];

        while ($linha = $resultado->fetch_assoc()) {
            $tarefas[] = $linha;
        }

        $stmt->close();
        return $tarefas;
    }

    public function update(Tarefa $tarefa): bool
    {
        $stmt = $this->conn->prepare(
            "UPDATE tarefa SET titulo = ?, descricao = ?, data_entrega = ? WHERE id = ?"
        );

        if (!$stmt) return false;

        $stmt->bind_param(
            "sssi",
            $tarefa->titulo,
            $tarefa->descricao,
            $tarefa->dataEntrega,
            $tarefa->id
        );

        $executou = $stmt->execute();
        $stmt->close();
        return $executou;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM tarefa WHERE id = ?");

        if (!$stmt) return false;

        $stmt->bind_param("i", $id);
        $executou = $stmt->execute();
        $deletou = $stmt->affected_rows > 0;

        $stmt->close();
        return $deletou;
    }
}
```

### 6. **Controller** (`src/controllers/TarefaController.php`)

Orquestra tudo. Sempre estende `BaseController`.

```php
<?php
namespace App\Controllers;

use App\DTOs\TarefaDTO;
use App\Services\TarefaService;

class TarefaController extends BaseController
{
    private TarefaService $service;

    public function __construct($conn)
    {
        parent::__construct($conn);
        $this->service = new TarefaService($conn);
    }

    public function index(): void
    {
        try {
            $result = $this->service->findAll();
            $this->json($result, $result['status'] ?? 200);
        } catch (\Throwable $e) {
            $this->handleException($e, 'index');
        }
    }

    public function create(): void
    {
        try {
            $dto = new TarefaDTO($_POST);
            $result = $this->service->create($dto);
            $this->json($result, $result['status'] ?? 200);
        } catch (\Throwable $e) {
            $this->handleException($e, 'create');
        }
    }

    public function update(): void
    {
        try {
            $dto = new TarefaDTO($_POST);
            $result = $this->service->update($dto);
            $this->json($result, $result['status'] ?? 200);
        } catch (\Throwable $e) {
            $this->handleException($e, 'update');
        }
    }

    public function delete(): void
    {
        try {
            $dto = new TarefaDTO($_POST);
            $result = $this->service->delete($dto);
            $this->json($result, $result['status'] ?? 200);
        } catch (\Throwable $e) {
            $this->handleException($e, 'delete');
        }
    }
}
```

## 🚀 Criando um Novo Módulo

### Passo 1: Criar a Tabela no BD

```sql
CREATE TABLE novo_modulo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
```

### Passo 2: Criar Model

`src/models/NovoModulo.php`

```php
<?php
namespace App\Models;

class NovoModulo
{
    public int $id;
    public string $nome;
    public string $descricao;

    public function __construct(string $nome, string $descricao, int $id = 0)
    {
        $this->id = $id;
        $this->nome = $nome;
        $this->descricao = $descricao;
    }
}
```

### Passo 3: Criar DTO

`src/dtos/NovoModuloDTO.php`

### Passo 4: Criar Validator

`src/validators/NovoModuloValidator.php`

### Passo 5: Criar Repository

`src/repositories/NovoModuloRepository.php`

### Passo 6: Criar Service

`src/services/NovoModuloService.php`

### Passo 7: Criar Controller

`src/controllers/NovoModuloController.php`

### Passo 8: Criar API File

`src/api/novo_modulo/index.php`

### Passo 9: Criar Frontend JS

`public/assets/api/novo_modulo/create.js`
`public/assets/api/novo_modulo/read.js`
`public/assets/api/novo_modulo/update.js`
`public/assets/api/novo_modulo/delete.js`

## 📋 Checklist de Desenvolvimento

- [ ] Tabela criada no BD
- [ ] Model criado
- [ ] DTO criado (aceita snake_case e camelCase)
- [ ] Validator criado (recebe DTO)
- [ ] Repository criado (checked `if (!$stmt)`)
- [ ] Service criado (usa validator e repository)
- [ ] Controller criado (estende BaseController)
- [ ] API file criado (roteia requisições)
- [ ] Frontend JS criado (CRUD)
- [ ] Documentação atualizada

## 🔐 Padrões de Segurança

✅ **Sempre fazer:**
- Prepared statements com `bind_param`
- Validar entrada com DTOs
- Usar BaseController para centralized error handling
- Verificar `if (!$stmt)` em operações BD
- Castear tipos em DTOs

❌ **NUNCA fazer:**
- Concatenar SQL (SQL injection)
- Confiar direto em `$_POST` e `$_GET`
- Echoed diretamente sen json_encode
- Esquecer `try/catch` em Controllers

---

Documentação de desenvolvimento criada! 📚
