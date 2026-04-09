<?php
/**
 * GET /BackEnd/api/admin/get_low_stock_products.php
 * Lấy danh sách sản phẩm sắp hết hàng
 */
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../config/db_connect.php';

try {
    $sql = "
        SELECT 
            p.id,
            p.name,
            p.brand,
            p.stock,
            p.low_stock_threshold,
            p.price,
            c.name as category
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.stock <= p.low_stock_threshold
        ORDER BY p.stock ASC, p.name ASC
    ";

    $result = $conn->query($sql);
    $products = [];

    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $products,
        'count' => count($products)
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi server: ' . $e->getMessage()
    ]);
}
?>
