<?php
/**
 * Admin API: Get single product for editing
 * GET /BackEnd/api/admin/get_product.php?id=123
 */
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/db_connect.php';

try {
    if (!$conn) {
        throw new Exception('Kết nối cơ sở dữ liệu thất bại');
    }
    
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'ID sản phẩm không hợp lệ'
        ]);
        exit;
    }
    
    $id = intval($_GET['id']);
    
    // Auto-add columns if they don't exist
    $checkColsQuery = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
                      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' 
                      AND COLUMN_NAME IN ('is_long_stock', 'long_stock_reason')";
    $colResult = @$conn->query($checkColsQuery);
    $existingCols = [];
    if ($colResult) {
        while ($colRow = $colResult->fetch_assoc()) {
            $existingCols[] = $colRow['COLUMN_NAME'];
        }
    }
    
    if (!in_array('is_long_stock', $existingCols)) {
        @$conn->query("ALTER TABLE products ADD COLUMN is_long_stock TINYINT DEFAULT 0");
    }
    if (!in_array('long_stock_reason', $existingCols)) {
        @$conn->query("ALTER TABLE products ADD COLUMN long_stock_reason TEXT");
    }
    
    $stmt = $conn->prepare("
        SELECT 
            p.id,
            p.name,
            p.brand,
            p.category_id,
            c.name as category_name,
            p.price,
            p.cost_price,
            p.profit_margin,
            p.stock,
            p.description,
            p.image_url,
            COALESCE(p.is_long_stock, 0) as is_long_stock,
            COALESCE(p.long_stock_reason, '') as long_stock_reason,
            p.created_at,
            p.updated_at
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.id = ?
        LIMIT 1
    ");
    
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Sản phẩm không tồn tại'
        ]);
        exit;
    }
    
    $product = $result->fetch_assoc();
    $stmt->close();
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $product
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage()
    ]);
}
?>
