# 🛣️ Roteamento (Routing)

Documentação do sistema de roteamento centralizado via `index.php` e `.htaccess`.

---

## 📋 Visão Geral

Antes (estrutura legada):
```
/src/api/tarefas/create.php
/src/api/tarefas/list.php
/src/api/auth/login.php
```

Agora (router centralizado):
```
/api/tarefas/create     → index.php → TarefaController::create()
/api/auth/login         → index.php → AuthController::login()
```

---

## ⚙️ Como Funciona

### 1. `.htaccess` - Rewrite de URLs

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    # Ignore arquivos e diretórios reais
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    # Reescreve tudo para index.php
    RewriteRule ^ index.php [QSA,L]
</IfModule>
```

**O que faz:**
1. Ativa motor de rewrite
2. Se arquivo real (`-f`) ou diretório real (`-d`), não reescreve
3. Caso contrário, roteia para `index.php` com query string (`QSA`)

### 2. `index.php` - Router Centralizado

```php
<?php
require_once __DIR__ . '/app/config/config.php';

// Extrai caminho da requisição
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remove prefixo /owl-school
$path = str_replace('/owl-school', '', $path);

// Extrai recurso e ação
// /api/tarefas/create → ['tarefas', 'create']
preg_match('/\/api\/([a-z_]+)\/([a-z_]+)/i', $path, $matches);

$resource = $matches[1] ?? 'home';
$action   = $matches[2] ?? 'index';

// Monta nome do controller
// 'tarefas' → 'TarefaController'
$controllerName = ucfirst(rtrim($resource, 's')) . 'Controller';
$controllerClass = "App\\Http\\Controllers\\{$controllerName}";

// Instancia e executa
if (class_exists($controllerClass)) {
    $controller = new $controllerClass($conn);
    $controller->$action();
} else {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Endpoint não encontrado']);
}
?>
```

---

## 📚 Exemplos de Rotas

### Tarefas (CRUD)

```
POST   /api/tarefas/create   → TarefaController::create()
GET    /api/tarefas/list     → TarefaController::index()
PUT    /api/tarefas/update   → TarefaController::update()
DELETE /api/tarefas/delete   → TarefaController::delete()
```

### Autenticação

```
POST   /api/auth/login       → AuthController::login()
GET    /api/auth/logout      → AuthController::logout()
GET    /api/auth/authme      → AuthController::authme()
```

### IA Assistente

```
POST   /api/ia/chat          → AIController::index()
POST   /api/ia/create        → AIController::create() (alias)
```

### Provas

```
POST   /api/provas/create    → ProvaController::create()
GET    /api/provas/list      → ProvaController::index()
```

### Comunicados

```
POST   /api/comunicados/create → ComunicadoController::create()
GET    /api/comunicados/list   → ComunicadoController::index()
```

---

## 🔧 Adicionando Nova Rota

Para adicionar novo recurso:

### 1. Criar Controller

```php
// app/Http/Controllers/NovoController.php
namespace App\Http\Controllers;

class NovoController extends BaseController {
    private NovoService $service;
    
    public function __construct($conn) {
        parent::__construct($conn);
        $this->service = new NovoService($conn);
    }
    
    public function create(): void {
        // Implementar...
    }
}
```

### 2. Usar a Rota

O router automaticamente mapeia:
```
/api/novo/create  →  NovoController::create()
/api/novo/list    →  NovoController::index()
```

---

## 🎯 Mapeamento Automático

O router segue este padrão:

```
/api/{recurso}/{acao}

recurso → Controlador
  tarefas     → TarefaController
  provas      → ProvaController
  chamada     → ChamadaController
  comunicados → ComunicadoController
  ia          → AIController
  auth        → AuthController

acao → Método do Controller
  create  → create()
  list    → index()
  update  → update()
  delete  → delete()
  chat    → index() (especial para IA)
```

---

## 🚀 Vantagens

✅ **URLs Amigáveis**: `/api/tarefas/create` sem `.php`
✅ **Roteamento Centralizado**: Um único ponto de entrada
✅ **Fácil Manutenção**: Adicionar rotas não requer nuevos arquivos
✅ **Segurança**: Oculta estrutura interna
✅ **SEO-Friendly**: URLs legíveis
✅ **Extensível**: Fácil adicionar middlewares globais
✅ **Sem Duplicação**: Sem múltiplos `index.php`

---

## 📝 Configuração no .env

```env
APP_URL=http://localhost/owl-school
APP_DEBUG=false
```

---

## 🔍 Troubleshooting

### Erro 404 em todas as rotas

Verifique se `.htaccess` está sendo lido:

```bash
# Verificar se mod_rewrite está ativo no Apache
a2enmod rewrite
systemctl restart apache2
```

### Rotas funcionam em localhost mas não em produção

Certifique-se que o servidor permite `.htaccess`:

```apache
# Em httpd.conf
<Directory /var/www/owl-school>
    AllowOverride All
</Directory>
```

### Query strings não funcionam

Adicione `QSA` (Query String Append) no `.htaccess`:

```apache
RewriteRule ^ index.php [QSA,L]
```
