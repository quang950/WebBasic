<?php
session_start();

require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../controllers/CartController.php';

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
if (!$data || !isset($data['product_id'], $data['quantity'])) {
    echo json_encode(['success' => false, 'message' => 'Thiếu dữ liệu sản phẩm']);
    exit;
}

// ép user theo session
$data['user_id'] = $userId;

$controller = new CartController($conn);
$result = $controller->add($data);

echo json_encode($result);
?>