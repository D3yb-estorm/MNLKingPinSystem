<?php
header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate');

$storageFile = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'gcash_qr.json';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!is_file($storageFile)) {
        echo json_encode(['gcashQRCode' => null]);
        exit;
    }

    $saved = json_decode(file_get_contents($storageFile), true);
    echo json_encode(['gcashQRCode' => $saved['gcashQRCode'] ?? null]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$payload = json_decode(file_get_contents('php://input'), true);
$qrCode = $payload['gcashQRCode'] ?? '';

if (!is_string($qrCode) || !preg_match('/^data:image\/(png|jpeg|jpg|gif);base64,/', $qrCode)) {
    http_response_code(400);
    echo json_encode(['error' => 'A valid QR image is required']);
    exit;
}

if (strlen($qrCode) > 5 * 1024 * 1024) {
    http_response_code(413);
    echo json_encode(['error' => 'QR image is too large']);
    exit;
}

$directory = dirname($storageFile);
if (!is_dir($directory)) {
    mkdir($directory, 0775, true);
}

if (file_put_contents($storageFile, json_encode(['gcashQRCode' => $qrCode])) === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Unable to save QR image']);
    exit;
}

echo json_encode(['ok' => true]);
