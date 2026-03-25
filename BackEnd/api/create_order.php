<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($data['userId'], $data['items']) || !is_array($data['items'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$userId = intval($data['userId']);
$items = $data['items'];
$shippingAddress = htmlspecialchars($data['shippingAddress'] ?? '');
$shippingPhone = htmlspecialchars($data['shippingPhone'] ?? '');
$totalPrice = floatval($data['totalPrice'] ?? 0);

// Create order
$stmt = $conn->prepare("
    INSERT INTO orders (user_id, total_price, shipping_address, shipping_phone, status, created_at)
    VALUES (?, ?, ?, ?, 'pending', NOW())
");

$stmt->bind_param("idss", $userId, $totalPrice, $shippingAddress, $shippingPhone);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to create order']);
    $stmt->close();
    exit;
}

$orderId = $stmt->insert_id;
$stmt->close();

// Insert order items
$stmtItem = $conn->prepare("
    INSERT INTO order_items (order_id, product_name, quantity, unit_price)
    VALUES (?, ?, ?, ?)
");

foreach ($items as $item) {
    $productName = htmlspecialchars($item['name'] ?? '');
    $quantity = intval($item['quantity'] ?? 1);
    $unitPrice = floatval($item['price'] ?? 0);
    
    $stmtItem->bind_param("isid", $orderId, $productName, $quantity, $unitPrice);
    if (!$stmtItem->execute()) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to add order items']);
        $stmtItem->close();
        exit;
    }
}

$stmtItem->close();

http_response_code(201);
echo json_encode([
    'success' => true,
    'message' => 'Order created successfully',
    'orderId' => $orderId
]);

$conn->close();
?>
