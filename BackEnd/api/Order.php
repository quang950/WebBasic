<?php
session_start();
require_once __DIR__ . '/../controllers/OrderController.php';

//  LẤY USER TỪ SESSION
$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    echo json_encode([
        'success' => false,
        'message' => 'User not logged in'
    ]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

// ÉP user_id THEO SESSION
$data['user_id'] = $userId;

$controller = new OrderController();
$order_id = $controller->create($data);

echo json_encode([
    'success' => true,
    'order_id' => $order_id
]);
?>