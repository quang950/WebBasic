<?php
/**
 * Test script: Create sample orders for testing
 * Access: localhost/WebBasic/create_test_orders.php
 */

require_once 'BackEnd/config/db_connect.php';

// Clear existing test orders
$conn->query("DELETE FROM order_details WHERE order_id > 1000");
$conn->query("DELETE FROM orders WHERE id > 1000");

// Get a regular user (not admin)
$stmt = $conn->prepare("SELECT id FROM users WHERE is_admin = 0 LIMIT 1");
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$user_id = $user['id'] ?? 2;

// Get a product
$stmt = $conn->prepare("SELECT id FROM products LIMIT 1");
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$product_id = $product['id'] ?? 1;

// Get product price
$stmt = $conn->prepare("SELECT price FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$prod = $result->fetch_assoc();
$price = $prod['price'] ?? 1000000;

// Create test orders
for ($i = 0; $i < 3; $i++) {
    $total = $price * (1 + $i);
    $address = "123 Đường ABC, Quận " . (1 + $i) . ", TP.HCM";
    $phone = "090" . (1000000 + $i);
    $status = ['new', 'processing', 'delivered'][$i];
    
    $stmt = $conn->prepare("
        INSERT INTO orders (user_id, total_price, shipping_address, shipping_phone, status, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param("isss", $user_id, $total, $address, $phone, $status);
    if ($stmt->execute()) {
        $order_id = $conn->insert_id;
        
        // Add order detail
        $qty = 1 + $i;
        $stmt2 = $conn->prepare("
            INSERT INTO order_details (order_id, product_id, quantity, price)
            VALUES (?, ?, ?, ?)
        ");
        $stmt2->bind_param("iiis", $order_id, $product_id, $qty, $price);
        $stmt2->execute();
        
        echo "✓ Created order #$order_id<br>";
    }
}

echo "<br><strong>Test orders created successfully!</strong>";
?>
