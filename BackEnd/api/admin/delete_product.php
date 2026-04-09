<?php
/**
 * Admin API: Delete product
 * POST /BackEnd/api/admin/delete_product.php
 */
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Chỉ chấp nhận POST request'
    ]);
    exit;
}

require_once __DIR__ . '/../../config/db_connect.php';

if (!$conn) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection error']);
    exit;
}

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
    $stmt = $conn->prepare("SELECT id, name FROM products WHERE id = ? LIMIT 1");
    if (!$stmt) {
        throw new Exception($conn->error);
    }
    
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Sản phẩm không tồn tại'
        ]);
        $stmt->close();
        exit;
    }
    
    $product = $result->fetch_assoc();
    $stmt->close();
    
    // Delete product
    $delete_stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    if (!$delete_stmt) {
        throw new Exception($conn->error);
    }
    
    $delete_stmt->bind_param('i', $id);
    
    if (!$delete_stmt->execute()) {
        throw new Exception($delete_stmt->error);
    }
    
    $delete_stmt->close();
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Xóa sản phẩm "' . $product['name'] . '" thành công'
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage()
    ]);
}
?>
