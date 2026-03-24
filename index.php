<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/db/conexao.php';

use App\Utils\Logger;

header('Content-Type: application/json');

// Parse da URL
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', trim($uri, '/'));

// Expected: /owl-school/api/resource/action
// uri[0] = owl-school
// uri[1] = api
// uri[2] = resource
// uri[3] = action (optional)

if (!isset($uri[1]) || $uri[1] !== 'api') {
    // Not an API request, serve static files
    header('Content-Type: text/html');
    http_response_code(404);
    echo file_get_contents(__DIR__ . '/public/index.html');
    exit();
}

if (!isset($uri[2])) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Rota não encontrada']);
    exit();
}

$resource = strtolower($uri[2]); // tarefa, prova, agenda, etc
$action = isset($uri[3]) ? strtolower($uri[3]) : null;

// Se não houver action no path, verificar query string ?action=
$actionFromQuery = false;
if (!$action && isset($_GET['action'])) {
    $action = $_GET['action']; // Não fazer lowercase para query string (vem em camelCase)
    $actionFromQuery = true; // Marcar que ação veio de query string
}

// Mapeamento de recursos para controllers
$controllerMap = [
    'tarefa' => 'TarefaController',
    'prova' => 'ProvaController',
    'prova_nota' => 'ProvaNotaController',
    'agenda' => 'AgendaController',
    'chamada' => 'ChamadaController',
    'chamada_item' => 'ChamadaItemController',
    'comunicado' => 'ComunicadoController',
    'advertencia' => 'AdvertenciaController',
    'ia' => 'AIController',
    'login' => 'AuthController',
    'logout' => 'AuthController',
    'authme' => 'AuthController',
    'utils' => 'UtilsController',
    'utils_aluno' => 'UtilsAlunoController',
    'utils_responsavel' => 'UtilsResponsavelController',
];

// Verificar se recurso existe
if (!isset($controllerMap[$resource])) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => "Recurso '{$resource}' não encontrado"]);
    exit();
}

try {
    // Carregar o controller dinamicamente
    $controllerClass = "App\\Http\\Controllers\\" . $controllerMap[$resource];
    
    if (!class_exists($controllerClass)) {
        throw new Exception("Controller {$controllerClass} não existe");
    }

    // Instanciar controller
    $controller = new $controllerClass($conn);

    // Mapear método HTTP + action para método do controller
    $method = $_SERVER['REQUEST_METHOD'];
    
    // Determinar o nome do método a chamar
    // Se ação vem da query string, chama index() para processar internamente
    // Se ação vem do PATH (uri[3]), chama o método direto
    
    if ($actionFromQuery) {
        // Query string: sempre chamar index()
        $methodName = 'index';
    } elseif ($action) {
        // Path: chamar método específico
        $methodName = $action;
    } else {
        // Nenhuma ação específica
        // Prioridade 1: verificar se o resource em si é um método (ex: login, logout)
        if (method_exists($controller, $resource)) {
            $methodName = $resource;
        } else {
            // Prioridade 2: mapear pelo método HTTP (apenas para CRUD padrão)
            $httpMethodMap = [
                'GET' => 'index',
                'POST' => 'create',
                'PUT' => 'update',
                'DELETE' => 'delete',
            ];
            
            $methodName = $httpMethodMap[$method] ?? 'index';
        }
    }

    // Validar se o método existe
    if (!method_exists($controller, $methodName)) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => "Método '{$methodName}' não encontrado em {$resource}"]);
        exit();
    }

    // Manipular dados de entrada para PUT/DELETE
    if ($method === 'PUT' || $method === 'DELETE') {
        $input = json_decode(file_get_contents('php://input'), true);
        $_POST = is_array($input) ? $input : [];
    }

    // Executar ação
    $controller->$methodName();

} catch (\Throwable $e) {
    Logger::error("Roteador: " . $e->getMessage() . " | Trace: " . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao processar requisição'
    ]);
}
