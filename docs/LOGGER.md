# Logger - Sistema de Logs Profissional

## 📝 Visão Geral

O `Logger` é um sistema centralizado para registrar eventos da aplicação. Todos os eventos são salvos em arquivos de log para posterior análise.

## 📁 Estrutura de Logs

```
logs/
├── app.log          (Todos os eventos)
└── errors.log       (Apenas erros críticos)
```

## 🔧 Como Usar

### 1. Importar o Logger

```php
use App\Utils\Logger;
```

### 2. Registrar Eventos

**ERROR - Erros Críticos**
```php
Logger::error('Erro ao conectar BD', [
    'host' => 'localhost',
    'error' => $exception->getMessage()
]);
```

**WARNING - Avisos**
```php
Logger::warning('Tentativa de login falhada', [
    'email' => 'usuario@example.com'
]);
```

**INFO - Informações**
```php
Logger::info('Usuário autenticado com sucesso', [
    'user_id' => 123,
    'email' => 'user@example.com'
]);
```

**DEBUG - Debug (Desenvolvimento)**
```php
Logger::debug('Dados recebidos', $_POST);
```

### 3. Usar Com Try/Catch

```php
try {
    // ... seu código ...
    $resultado = $this->repository->create($objeto);
    
    if (!$resultado) {
        throw new Exception('Erro ao criar registro');
    }
    
    Logger::info('Registro criado com sucesso', ['id' => $resultado]);
    
} catch (\Exception $e) {
    Logger::error('Exceção capturada', [
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno do servidor.'
    ]);
}
```

## 📊 Formato de Log

```
[2024-03-11 14:30:45] [ERROR] Erro ao conectar BD | {"host":"localhost","error":"Connection refused"}
[2024-03-11 14:31:12] [WARNING] Tentativa de login falhada | {"email":"usuario@example.com"}
[2024-03-11 14:32:00] [INFO] Usuário autenticado com sucesso | {"user_id":123,"email":"user@example.com"}
```

## 🎯 Exemplo Prático - TarefaController

```php
namespace App\Controllers;

use App\Utils\Logger;

class TarefaController {
    public function create() {
        try {
            // Validar dados
            $validacao = $this->service->validarCreate($_POST);
            if (!$validacao['success']) {
                Logger::warning('Validação falhou', $_POST);
                echo json_encode($validacao);
                return;
            }

            // Criar registro
            $criou = $this->repository->create($tarefa);

            if ($criou) {
                Logger::info('Tarefa criada com sucesso', ['titulo' => $tarefa->titulo]);
                echo json_encode(['success' => true, 'message' => 'Criado com sucesso']);
                return;
            }

            Logger::error('Erro ao criar tarefa', $_POST);
            echo json_encode(['success' => false, 'message' => 'Erro ao criar']);
            
        } catch (\Exception $e) {
            Logger::error('Exceção em TarefaController::create', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erro interno']);
        }
    }
}
```

## 🔍 Analisar Logs

### Via PHP (Para debug rápido)
```php
$ultimasLinhas = Logger::getLastLines(50); // Últimas 50 linhas
foreach ($ultimasLinhas as $linha) {
    echo htmlspecialchars($linha) . "<br>";
}
```

### Via Terminal
```bash
# Ver últimas 50 linhas de app.log
tail -50 logs/app.log

# Ver todos os errors.log
cat logs/errors.log

# Buscar erros de um usuário
grep "user_id=123" logs/app.log
```

## 📌 Boas Práticas

✅ **Sempre use try/catch em operações críticas** (banco de dados, arquivos)
✅ **Registre tentativas de operações importantes** (login, criação, deleção)
✅ **Inclua contexto útil** (IDs, dados relevantes, mensagens claras)
✅ **Use o nível correto de log** (ERROR, WARNING, INFO, DEBUG)
✅ **Nunca exponha detalhes técnicos ao usuário** (use mensagens genéricas)

## 🚀 Próximas Etapas

- [ ] Estender Logger a todos os Controllers
- [ ] Implementar rotação automática de logs (arquivo por dia)
- [ ] Criar dashboard para visualizar logs
- [ ] Enviar alerts de ERROR por email
