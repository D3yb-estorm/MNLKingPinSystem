<?php
header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$storageFile = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'products.json';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!is_file($storageFile)) {
        echo json_encode(['products' => []]);
        exit;
    }

    $saved = json_decode(file_get_contents($storageFile), true);
    echo json_encode(['products' => is_array($saved['products'] ?? null) ? $saved['products'] : []]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$payload = json_decode(file_get_contents('php://input'), true);
$products = $payload['products'] ?? null;

if (!is_array($products)) {
    http_response_code(400);
    echo json_encode(['error' => 'Products must be an array']);
    exit;
}

if (strlen(json_encode($products)) > 25 * 1024 * 1024) {
    http_response_code(413);
    echo json_encode(['error' => 'Product data is too large']);
    exit;
}

$directory = dirname($storageFile);
if (!is_dir($directory)) {
    mkdir($directory, 0775, true);
}

if (file_put_contents($storageFile, json_encode(['products' => $products])) === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Unable to save products']);
    exit;
}

echo json_encode(['ok' => true]);
