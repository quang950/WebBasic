<?php
/**
 * Admin API: Add new category
 * POST /BackEnd/api/admin/add_category.php
 */
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/db_connect.php';

if (!$conn) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection error']);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate
    if (!isset($data['name']) || empty(trim($data['name']))) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Tên loại sản phẩm không được để trống'
        ]);
        exit;
    }
    
    $name = trim($data['name']);
    $description = trim($data['description'] ?? '');
    
    // Check duplicate
    $stmt = $conn->prepare("SELECT id FROM categories WHERE name = ? LIMIT 1");
    $stmt->bind_param('s', $name);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        http_response_code(409);
        echo json_encode([
            'success' => false,
            'message' => 'Tên loại sản phẩm này đã tồn tại'
        ]);
        exit;
    }
    
    // Insert
    $stmt = $conn->prepare("INSERT INTO categories (name, description, status) VALUES (?, ?, 1)");
    $stmt->bind_param('ss', $name, $description);
    
    if ($stmt->execute()) {
        $category_id = $conn->insert_id;
        
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Thêm loại sản phẩm thành công',
            'category_id' => $category_id
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
