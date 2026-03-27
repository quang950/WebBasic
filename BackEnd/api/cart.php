<?php
// cart.php
// Calls CartController

require_once '../controllers/CartController.php';

header("Content-Type: application/json");

$controller = new CartController();

// lấy action từ URL
$action = $_GET['action'] ?? '';

if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $user_id = $data['user_id'] ?? null;
    $product_id = $data['product_id'] ?? null;
    $quantity = $data['quantity'] ?? 1;

    echo json_encode($controller->addToCart($user_id, $product_id, $quantity));
}
elseif ($action === 'get' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $user_id = $_GET['user_id'] ?? null;

    echo json_encode($controller->getCart($user_id));
}
elseif ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $cart_id = $data['cart_id'] ?? null;

    echo json_encode($controller->deleteCart($cart_id));
}
elseif ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $cart_id = $data['cart_id'] ?? null;
    $quantity = $data['quantity'] ?? null;

    echo json_encode($controller->updateQuantity($cart_id, $quantity));
}
else {
    echo json_encode(["error" => "Invalid action"]);
}