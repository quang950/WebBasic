<?php
require_once '../controllers/OrderController.php';

header("Content-Type: application/json");

// chỉ cho phép POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["error" => "Method not allowed"]);
    exit;
}

// lấy dữ liệu JSON từ body
$data = json_decode(file_get_contents("php://input"), true);

// kiểm tra dữ liệu
$user_id = $data['user_id'] ?? null;
$address = $data['address'] ?? null;
$payment_method = $data['payment_method'] ?? null;

$controller = new OrderController();

$result = $controller->createOrder($user_id, $address, $payment_method);

echo json_encode($result);