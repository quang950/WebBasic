<?php
/**
 * Debug API - Kiểm tra REQUEST_METHOD và các header
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: *');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

echo json_encode([
    'request_method' => $_SERVER['REQUEST_METHOD'],
    'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'Not set',
    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
    'php_version' => phpversion(),
    'all_headers' => getallheaders(),
    'request_body_readable' => file_get_contents('php://input'),
    'timestamp' => date('Y-m-d H:i:s'),
    'message' => 'Nếu bạn nhìn thấy "request_method": "POST" thì server hoạt động bình thường'
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
