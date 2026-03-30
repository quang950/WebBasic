<?php
session_start();

header('Content-Type: application/json');
require_once __DIR__ . '/../config/db_connect.php';

//LẤY USER TỪ SESSION
$userId = $_SESSION['user_id'] ?? null;

// Nếu chưa đăng nhập → trả về rỗng
if (!$userId) {
    echo json_encode([
        'success' => true,
        'orders' => []
    ]);
    exit;
}

// Get user's orders
$stmt = $conn->prepare("
    SELECT id, total_price, shipping_address, shipping_phone, status, created_at
    FROM orders
    WHERE user_id = ?
    ORDER BY created_at DESC
");

$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orderId = $row['id'];
    
    // Get items for this order
    $stmtItems = $conn->prepare("
        SELECT product_name, quantity, unit_price
        FROM order_items
        WHERE order_id = ?
    ");
    
    $stmtItems->bind_param("i", $orderId);
    $stmtItems->execute();
    $itemsResult = $stmtItems->get_result();
    
    $items = [];
    while ($itemRow = $itemsResult->fetch_assoc()) {
        $items[] = $itemRow;
    }
    $stmtItems->close();
    
    $row['items'] = $items;
    $orders[] = $row;
}

$stmt->close();

http_response_code(200);
echo json_encode([
    'success' => true,
    'orders' => $orders
]);

$conn->close();
?>
