<?php
require_once __DIR__ . '/../config/db_connect.php';

if (!$dbConnected) {
    die("Lỗi DB: " . $dbError);
}

$id = (int)$_GET['id'];

$result = $conn->query("
    SELECT * FROM order_items 
    WHERE product_name IN (SELECT name FROM products WHERE id = $id)
");

if ($result->num_rows > 0) {
    $conn->query("UPDATE products SET status='hidden' WHERE id=$id");
} else {
    $conn->query("DELETE FROM products WHERE id=$id");
}

header("Location: products.php");