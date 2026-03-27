<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/db/conexao.php';

use App\Utils\Logger;

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

try {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $basePath = '/owl-school';

    if (str_starts_with($path, $basePath)) {
        $path = substr($path, strlen($basePath));
    }

    $uri = explode('/', trim($path, '/'));

    // Esperado após remover /owl-school:
    // /api/resource/action
    // uri[0] = api
    // uri[1] = resource
    // uri[2] = action (optional)

    if (!isset($uri[0]) || $uri[0] !== 'api') {
        header('Content-Type: text/html; charset=utf-8');
        http_response_code(200);
        readfile(__DIR__ . '/public/index.html');
        exit();
    }

    if (!isset($uri[1]) || empty($uri[1])) {
        jsonResponse([
            'success' => false,
            'message' => 'Rota não encontrada'
        ], 404);
    }

    $resource = strtolower($uri[1]);
    $action = isset($uri[2]) ? strtolower($uri[2]) : null;

    $actionFromQuery = false;

    if (!$action && isset($_GET['action']) && is_string($_GET['action'])) {
        $action = preg_replace('/[^a-zA-Z0-9_]/', '', $_GET['action']);
        $actionFromQuery = true;
    }

    if (!isset($controllerMap[$resource])) {
        jsonResponse([
            'success' => false,
            'message' => "Recurso '{$resource}' não encontrado"
        ], 404);
    }

    $controllerClass = "App\\Http\\Controllers\\" . $controllerMap[$resource];

    if (!class_exists($controllerClass)) {
        throw new Exception("Controller {$controllerClass} não existe");
    }

    $controller = new $controllerClass($conn);
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    if ($actionFromQuery) {
        $methodName = 'index';
    } elseif ($action) {
        $methodName = $action;
    } else {
        if (method_exists($controller, $resource)) {
            $methodName = $resource;
        } else {
            $httpMethodMap = [
                'GET' => 'index',
                'POST' => 'create',
                'PUT' => 'update',
                'DELETE' => 'delete',
            ];

            $methodName = $httpMethodMap[$method] ?? 'index';
        }
    }

    if (!method_exists($controller, $methodName)) {
        jsonResponse([
            'success' => false,
            'message' => "Método '{$methodName}' não encontrado em {$resource}"
        ], 404);
    }

    if (in_array($method, ['PUT', 'DELETE', 'PATCH'], true)) {
        $input = json_decode(file_get_contents('php://input'), true);
        $_POST = is_array($input) ? $input : [];
    }

    $controller->$methodName();

} catch (\Throwable $e) {
    Logger::error("Roteador: " . $e->getMessage() . " | Trace: " . $e->getTraceAsString());

    jsonResponse([
        'success' => false,
        'message' => 'Erro ao processar requisição'
    ], 500);
}

function jsonResponse(array $data, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}