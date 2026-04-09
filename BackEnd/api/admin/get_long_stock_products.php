<?php
/**
 * Admin API: Get long stock products
 */
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/db_connect.php';

try {
    // Check database connection
    if (!$conn) {
        throw new Exception('Database connection failed');
    }
    
    // Check admin
    if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
        throw new Exception('Unauthorized');
    }
    
    $sql = "
        SELECT id, name, brand, stock, price, is_long_stock, long_stock_reason
        FROM products
        WHERE is_long_stock = 1
        ORDER BY id DESC
    ";
    
    $result = $conn->query($sql);
    if (!$result) {
        throw new Exception("Query error: " . $conn->error);
    }
    
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $row['is_long_stock'] = (int)$row['is_long_stock'];
        $products[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'products' => $products,
        'count' => count($products)
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
