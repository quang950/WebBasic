<?php
/**
 * Admin API: Delete product
 * POST /BackEnd/api/admin/delete_product.php
 * 
 * Logic:
 * - If product has NO stock history (never imported) → DELETE completely
 * - If product HAS stock history (already imported) → Mark as hidden (status = 0)
 */
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/db_connect.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'ID sản phẩm không hợp lệ'
        ]);
        exit;
    }
    
    $id = intval($data['id']);
    
    // Check product exists
    $stmt = $conn->prepare("SELECT id, name, status FROM products WHERE id = ? LIMIT 1");
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
    
    // Check if product has stock history (imports)
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM stock_history WHERE product_id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $has_history = $stmt->get_result()->fetch_assoc()['count'] > 0;
    
    if ($has_history) {
        // Mark as hidden instead of deleting
        $stmt = $conn->prepare("UPDATE products SET status = 0 WHERE id = ?");
        $stmt->bind_param('i', $id);
        
        if ($stmt->execute()) {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Sản phẩm "' . $product['name'] . '" đã được ẩn khỏi website (vì đã có nhập hàng)',
                'action' => 'hidden'
            ]);
        } else {
            throw new Exception($conn->error);
        }
    } else {
        // No history, delete completely from database
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param('i', $id);
        
        if ($stmt->execute()) {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Xóa sản phẩm "' . $product['name'] . '" thành công',
                'action' => 'deleted'
            ]);
        } else {
            throw new Exception($conn->error);
        }
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage()
    ]);
}
?>
