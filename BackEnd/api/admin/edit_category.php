<?php
/**
 * Admin API: Edit category
 * POST /BackEnd/api/admin/edit_category.php
 */
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/db_connect.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate
    if (!isset($data['id']) || !isset($data['name'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Thiếu thông tin bắt buộc'
        ]);
        exit;
    }
    
    $id = intval($data['id']);
    $name = trim($data['name']);
    $description = trim($data['description'] ?? '');
    $status = isset($data['status']) ? intval($data['status']) : 1;
    
    // Check if name exists (excluding current)
    $stmt = $conn->prepare("SELECT id FROM categories WHERE name = ? AND id != ? LIMIT 1");
    $stmt->bind_param('si', $name, $id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        http_response_code(409);
        echo json_encode([
            'success' => false,
            'message' => 'Tên loại sản phẩm này đã tồn tại'
        ]);
        exit;
    }
    
    // Check category exists
    $stmt = $conn->prepare("SELECT id FROM categories WHERE id = ? LIMIT 1");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Loại sản phẩm không tồn tại'
        ]);
        exit;
    }
    
    // Update
    $stmt = $conn->prepare("UPDATE categories SET name = ?, description = ?, status = ? WHERE id = ?");
    $stmt->bind_param('ssii', $name, $description, $status, $id);
    
    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Cập nhật loại sản phẩm thành công'
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
