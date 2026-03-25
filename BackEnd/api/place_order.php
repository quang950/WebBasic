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

if (!isset($data['userId']) || !isset($data['items']) || !is_array($data['items'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$userId = intval($data['userId']);
$items = $data['items'];
$totalPrice = floatval($data['totalPrice'] ?? 0);
$recipientName = htmlspecialchars($data['recipientName'] ?? '');
$recipientPhone = htmlspecialchars($data['recipientPhone'] ?? '');
$recipientAddress = htmlspecialchars($data['recipientAddress'] ?? '');

try {
    // Start transaction
    $conn->begin_transaction();
    
    // Create order
    $stmt = $conn->prepare("
        INSERT INTO orders (user_id, total_price, shipping_address, shipping_phone, status, created_at)
        VALUES (?, ?, ?, ?, 'pending', NOW())
    ");
    
    $stmt->bind_param("idss", $userId, $totalPrice, $recipientAddress, $recipientPhone);
    $stmt->execute();
    $orderId = $stmt->insert_id;
    $stmt->close();
    
    // Insert order items
    $stmtItem = $conn->prepare("
        INSERT INTO order_items (order_id, product_name, quantity, unit_price)
        VALUES (?, ?, ?, ?)
    ");
    
    foreach ($items as $item) {
        $productName = htmlspecialchars($item['name'] ?? 'Unknown Product');
        $quantity = intval($item['quantity'] ?? 1);
        $unitPrice = floatval($item['price'] ?? 0);
        
        $stmtItem->bind_param("isid", $orderId, $productName, $quantity, $unitPrice);
        $stmtItem->execute();
    }
    $stmtItem->close();
    
    // Commit transaction
    $conn->commit();
    
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Order placed successfully',
        'orderId' => $orderId
    ]);
    
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error creating order: ' . $e->getMessage()]);
}

$conn->close();
?>
