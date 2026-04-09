<?php
session_start();
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    echo json_encode(['success' => true]);
    exit;
}

require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../controllers/CartController.php';

if (!$conn) {
	http_response_code(500);
	echo json_encode(['success' => false, 'message' => 'Database connection error'], JSON_UNESCAPED_UNICODE);
	exit;
}

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập'], JSON_UNESCAPED_UNICODE);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
if (!$data || !isset($data['product_id'], $data['quantity'])) {
    echo json_encode(['success' => false, 'message' => 'Thiếu dữ liệu sản phẩm'], JSON_UNESCAPED_UNICODE);
    exit;
}

// ép user theo session
$data['user_id'] = $userId;

$controller = new CartController($conn);
$result = $controller->add($data);

echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>