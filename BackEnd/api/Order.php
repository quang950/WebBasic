<?php
session_start();
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once __DIR__ . '/../controllers/OrderController.php';

//  LẤY USER TỪ SESSION
$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'User not logged in',
        'sessionData' => $_SESSION
    ]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid JSON data',
        'rawInput' => file_get_contents("php://input")
    ]);
    exit;
}

// ÉP user_id THEO SESSION
$data['user_id'] = $userId;

try {
    $controller = new OrderController();
    $result = $controller->create($data);

    // Kiểm tra xem có lỗi hay không
    if (is_array($result) && isset($result['error'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $result['error']
        ]);
        exit;
    }

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'order_id' => $result
    ]);
} catch (Exception $e) {
    // Ghi log lỗi
    error_log("Order Error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage(),
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
?>