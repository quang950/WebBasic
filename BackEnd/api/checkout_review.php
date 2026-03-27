<?php
require_once __DIR__ . '/../config/db_connect.php';

$user_id = intval($_GET['user_id']);

$stmt = $conn->prepare("
    SELECT p.name, p.price, c.quantity
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$total = 0;
$items = [];

while ($row = $result->fetch_assoc()) {
    $row['subtotal'] = $row['price'] * $row['quantity'];
    $total += $row['subtotal'];
    $items[] = $row;
}

echo json_encode([
    "items" => $items,
    "total" => $total
]);