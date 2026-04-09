<?php
session_start();
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);
    exit;
}

$userId = $_SESSION['user_id'] ?? null;

http_response_code(200);
echo json_encode([
    'success' => true,
    'is_logged_in' => !is_null($userId),
    'user_id' => $userId
], JSON_UNESCAPED_UNICODE);
?>
