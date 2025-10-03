<?php
// public/index.php
// Allow CORS and JSON responses
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/../src/ProductController.php';

// parse incoming path
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// remove base path if hosted in subfolder (adjust if different)
$base = '/appliances_api/public';
$path = preg_replace("#^$base#", '', $uri);
$path = trim($path, '/');

$method = $_SERVER['REQUEST_METHOD'];
$segments = explode('/', $path);

// Expect endpoints: api/products and api/products/{id}
if ($segments[0] !== 'api' || $segments[1] !== 'products') {
    http_response_code(404);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Not found']);
    exit;
}

$controller = new ProductController();

// get JSON input body
$input = null;
if (in_array($method, ['POST','PUT','PATCH'])) {
    $raw = file_get_contents('php://input');
    $input = json_decode($raw, true);
    if ($raw && $input === null) {
        // invalid json
        http_response_code(400);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => 'Invalid JSON']);
        exit;
    }
}

// route
// GET /api/products
if ($method === 'GET' && count($segments) === 2) {
    $controller->index($_GET);
    exit;
}

// GET /api/products/{id}
if ($method === 'GET' && count($segments) === 3 && is_numeric($segments[2])) {
    $controller->show((int)$segments[2]);
    exit;
}

// POST /api/products
if ($method === 'POST' && count($segments) === 2) {
    $controller->create($input ?? []);
    exit;
}

// PUT/PATCH /api/products/{id}
if (in_array($method, ['PUT','PATCH']) && count($segments) === 3 && is_numeric($segments[2])) {
    $controller->update((int)$segments[2], $input ?? []);
    exit;
}

// DELETE /api/products/{id}
if ($method === 'DELETE' && count($segments) === 3 && is_numeric($segments[2])) {
    $controller->delete((int)$segments[2]);
    exit;
}

// default 405
http_response_code(405);
header('Content-Type: application/json; charset=utf-8');
echo json_encode(['error' => 'Method not allowed']);
exit;
