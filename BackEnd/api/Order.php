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
        'message' => 'User not logged in'
    ]);
    exit;
}

// Read input once and keep it in memory
$rawInput = file_get_contents("php://input");
$data = json_decode($rawInput, true);

if (!$data || !is_array($data)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid JSON data'
    ]);
    exit;
}

// Force user_id from SESSION (security)
$data['user_id'] = $userId;

try {
    $controller = new OrderController();
    $result = $controller->create($data);

    // Check for errors
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
    // Log error
    error_log("Order Error: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>