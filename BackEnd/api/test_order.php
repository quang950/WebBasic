<?php
session_start();
header('Content-Type: application/json');

// Simulate user session
$_SESSION['user_id'] = 1;

require_once __DIR__ . '/../models/OrderModel.php';

// Test data
$test_data = [
    'user_id' => 1,
    'shipping_address' => '123 Nguyen Van Linh, Q7, TP HCM',
    'shipping_phone' => '0901234567',
    'payment_method' => 'cod',
    'cart_items' => [
        [
            'name' => 'Toyota Camry',
            'price' => 1235000000,
            'quantity' => 1,
            'img' => 'test.jpg'
        ]
    ]
];

try {
    $orderModel = new OrderModel();
    $result = $orderModel->createOrder(
        $test_data['user_id'],
        $test_data['shipping_address'],
        $test_data['shipping_phone'],
        $test_data['payment_method'],
        $test_data['cart_items']
    );
    
    if (is_array($result) && isset($result['error'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => $result['error'],
            'test_data' => $test_data
        ]);
    } else {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'order_id' => $result,
            'test_data' => $test_data
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
        'test_data' => $test_data
    ]);
}
?>
