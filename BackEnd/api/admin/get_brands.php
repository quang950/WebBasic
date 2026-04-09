<?php
/**
 * Admin API: Get all brands
 * GET /BackEnd/api/admin/get_brands.php
 */
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/db_connect.php';

try {
    if (!$conn) {
        throw new Exception("Database connection failed");
    }
    
    // Get distinct brands from products table
    $sql = "
        SELECT DISTINCT 
            p.brand,
            COUNT(p.id) as product_count
        FROM products p
        WHERE p.brand IS NOT NULL AND p.brand != ''
        GROUP BY p.brand
        ORDER BY p.brand ASC
    ";
    
    $result = $conn->query($sql);
    
    if (!$result) {
        throw new Exception("Query error: " . $conn->error);
    }
    
    $brands = [];
    while ($row = $result->fetch_assoc()) {
        $brands[] = $row;
    }
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'brands' => $brands,
        'total' => count($brands)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage()
    ]);
}
?>
