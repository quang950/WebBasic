<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../models/OrderModel.php';

// Lấy user từ session
$userId = $_SESSION['user_id'] ?? null;

// Nếu chưa đăng nhập → trả rỗng
if (!$userId) {
    echo json_encode([
        'success' => true,
        'items' => [],
        'total_price' => 0
    ]);
    exit;
}

// Gọi OrderModel
$orderModel = new OrderModel();
$result = $orderModel->previewOrder($userId);

// Chuẩn hóa response cho frontend
echo json_encode([
    'success' => true,
    'items' => $result['items'] ?? [],
    'total_price' => $result['total_price'] ?? 0
]);
