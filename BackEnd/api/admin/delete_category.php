<?php
/**
 * Admin API: Delete category
 * POST /BackEnd/api/admin/delete_category.php
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
            'message' => 'ID loại sản phẩm không hợp lệ'
        ]);
        exit;
    }
    
    $id = intval($data['id']);
    
    // Check category exists
    $stmt = $conn->prepare("SELECT name FROM categories WHERE id = ? LIMIT 1");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Loại sản phẩm không tồn tại'
        ]);
        exit;
    }
    
    $category = $result->fetch_assoc();
    
    // Check for products in category
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM products WHERE category_id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $product_count = $stmt->get_result()->fetch_assoc()['count'];
    
    if ($product_count > 0) {
        http_response_code(409);
        echo json_encode([
            'success' => false,
            'message' => 'Không thể xóa loại sản phẩm này vì có ' . $product_count . ' sản phẩm thuộc loại này. Vui lòng xóa/chuyển các sản phẩm trước.'
        ]);
        exit;
    }
    
    // Delete
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param('i', $id);
    
    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Xóa loại sản phẩm "' . $category['name'] . '" thành công'
        ]);
    } else {
        throw new Exception($conn->error);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage()
    ]);
}
?>
