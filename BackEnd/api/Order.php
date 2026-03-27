<?php
require_once __DIR__ . '/../controllers/OrderController.php';

$data = json_decode(file_get_contents("php://input"), true);

$controller = new OrderController();
$order_id = $controller->create($data);

echo json_encode([
    "success" => true,
    "order_id" => $order_id
]);