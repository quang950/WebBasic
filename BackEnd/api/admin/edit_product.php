<?php
/**
 * Admin API: Edit product
 * POST /BackEnd/api/admin/edit_product.php
 */
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/db_connect.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate
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
    $stmt = $conn->prepare("SELECT id FROM products WHERE id = ? LIMIT 1");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Sản phẩm không tồn tại'
        ]);
        exit;
    }
    
    // Extract data
    $name = trim($data['name'] ?? '');
    $product_code = trim($data['product_code'] ?? '');
    $category_id = intval($data['category_id'] ?? 1);
    $price = floatval($data['price'] ?? 0);
    $price_cost = floatval($data['price_cost'] ?? 0);
    $profit_margin = floatval($data['profit_margin'] ?? 0);
    $stock = intval($data['stock'] ?? 0);
    $unit = trim($data['unit'] ?? 'chiếc');
    $description = trim($data['description'] ?? '');
    $image_url = trim($data['image_url'] ?? '');
    $status = isset($data['status']) ? intval($data['status']) : 1;
    
    // Build update query
    $update_fields = [];
    $update_values = [];
    $bind_types = '';
    
    if (!empty($name)) {
        $update_fields[] = 'name = ?';
        $update_values[] = $name;
        $bind_types .= 's';
    }
    
    if (!empty($product_code)) {
        $update_fields[] = 'product_code = ?';
        $update_values[] = $product_code;
        $bind_types .= 's';
    }
    
    if ($category_id > 0) {
        $update_fields[] = 'category_id = ?';
        $update_values[] = $category_id;
        $bind_types .= 'i';
    }
    
    if ($price > 0) {
        $update_fields[] = 'price = ?';
        $update_values[] = $price;
        $bind_types .= 'd';
    }
    
    if ($price_cost >= 0) {
        $update_fields[] = 'price_cost = ?';
        $update_values[] = $price_cost;
        $bind_types .= 'd';
    }
    
    if ($profit_margin >= 0) {
        $update_fields[] = 'profit_margin = ?';
        $update_values[] = $profit_margin;
        $bind_types .= 'd';
    }
    
    if (isset($data['stock'])) {
        $update_fields[] = 'stock = ?';
        $update_values[] = $stock;
        $bind_types .= 'i';
    }
    
    if (!empty($unit)) {
        $update_fields[] = 'unit = ?';
        $update_values[] = $unit;
        $bind_types .= 's';
    }
    
    if (!empty($description)) {
        $update_fields[] = 'description = ?';
        $update_values[] = $description;
        $bind_types .= 's';
    }
    
    if (!empty($image_url)) {
        $update_fields[] = 'image_url = ?';
        $update_values[] = $image_url;
        $bind_types .= 's';
    }
    
    $update_fields[] = 'status = ?';
    $update_values[] = $status;
    $bind_types .= 'i';
    
    if (empty($update_fields)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Không có dữ liệu cần cập nhật'
        ]);
        exit;
    }
    
    // Prepare update statement
    $update_values[] = $id;
    $bind_types .= 'i';
    
    $sql = "UPDATE products SET " . implode(', ', $update_fields) . " WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    $stmt->bind_param($bind_types, ...$update_values);
    
    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Cập nhật sản phẩm thành công'
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
