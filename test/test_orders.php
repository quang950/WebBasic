<?php
session_start();
require_once 'BackEnd/config/db_connect.php';

// Check orders
$stmt = $conn->prepare('SELECT COUNT(*) as cnt FROM orders');
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
echo "Total orders in database: " . $row['cnt'] . "\n";

// List all orders
$stmt = $conn->prepare('
    SELECT o.id, o.user_id, o.total_price, o.shipping_address, o.created_at,
           u.first_name, u.last_name, u.email
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    LIMIT 5
');
$stmt->execute();
$result = $stmt->get_result();
echo "\nFirst 5 orders:\n";
while ($row = $result->fetch_assoc()) {
    echo "Order #" . $row['id'] . " - " . $row['first_name'] . " " . $row['last_name'] . " - " . date('Y-m-d H:i', strtotime($row['created_at'])) . "\n";
}
?>
