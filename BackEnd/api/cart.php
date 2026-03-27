<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");

require_once __DIR__ . '/../controllers/CartController.php';

$controller = new CartController();
$action = $_GET['action'] ?? '';

// Lấy JSON body
$input = json_decode(file_get_contents("php://input"), true);

switch ($action) {

    // LẤY GIỎ HÀNG
    case 'get':
        if (!isset($_GET['user_id'])) {
            echo json_encode(["error" => "Thiếu user_id"]);
            break;
        }

        $user_id = intval($_GET['user_id']);
        echo json_encode($controller->get($user_id));
        break;

    //  THÊM VÀO GIỎ
    case 'add':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(["error" => "Phải dùng POST"]);
            break;
        }

        if (!isset($input['user_id'], $input['product_id'], $input['quantity'])) {
            echo json_encode(["error" => "Thiếu dữ liệu"]);
            break;
        }

        echo json_encode([
            "success" => $controller->add($input)
        ]);
        break;

    //  UPDATE GIỎ HÀNG
    case 'update':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(["error" => "Phải dùng POST"]);
            break;
        }

        if (!isset($input['cart_id'], $input['quantity'])) {
            echo json_encode(["error" => "Thiếu dữ liệu"]);
            break;
        }

        echo json_encode([
            "success" => $controller->update($input)
        ]);
        break;

    default:
        echo json_encode(["error" => "Action không hợp lệ"]);
        break;
}