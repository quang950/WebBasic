<?php
/**
 * Admin API: Edit product
 * POST /BackEnd/api/admin/edit_product.php
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
    http_response_code(503);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi kết nối cơ sở dữ liệu'
    ]);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!is_array($data)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Dữ liệu JSON không hợp lệ'
        ]);
        exit;
    }
    
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
    if (!$stmt) {
        throw new Exception($conn->error);
    }
    
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
    $stmt->close();
    
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
    
    // Extract data
    $name = isset($data['name']) ? trim($data['name']) : null;
    $brand = isset($data['brand']) ? trim($data['brand']) : null;
    $category_id = isset($data['category_id']) ? intval($data['category_id']) : null;
    $price = isset($data['price']) ? floatval($data['price']) : null;
    $cost_price = isset($data['cost_price']) ? floatval($data['cost_price']) : null;
    $profit_margin = isset($data['profit_margin']) ? floatval($data['profit_margin']) : null;
    $stock = isset($data['stock']) ? intval($data['stock']) : null;
    $description = isset($data['description']) ? trim($data['description']) : null;
    $image_url = isset($data['image_url']) ? trim($data['image_url']) : null;
    $is_long_stock = isset($data['is_long_stock']) ? intval($data['is_long_stock']) : null;
    $long_stock_reason = isset($data['long_stock_reason']) ? trim($data['long_stock_reason']) : null;
    
    // Build update query
    $update_fields = [];
    $update_values = [];
    $bind_types = '';
    
    if ($name !== null) {
        $update_fields[] = 'name = ?';
        $update_values[] = $name;
        $bind_types .= 's';
    }
    
    if ($brand !== null) {
        $update_fields[] = 'brand = ?';
        $update_values[] = $brand;
        $bind_types .= 's';
    }
    
    if ($category_id !== null) {
        $update_fields[] = 'category_id = ?';
        $update_values[] = $category_id;
        $bind_types .= 'i';
    }
    
    if ($price !== null) {
        $update_fields[] = 'price = ?';
        $update_values[] = $price;
        $bind_types .= 'd';
    }
    
    if ($cost_price !== null) {
        $update_fields[] = 'cost_price = ?';
        $update_values[] = $cost_price;
        $bind_types .= 'd';
    }
    
    if ($profit_margin !== null) {
        $update_fields[] = 'profit_margin = ?';
        $update_values[] = $profit_margin;
        $bind_types .= 'd';
    }
    
    if ($stock !== null) {
        $update_fields[] = 'stock = ?';
        $update_values[] = $stock;
        $bind_types .= 'i';
    }
    
    if ($description !== null) {
        $update_fields[] = 'description = ?';
        $update_values[] = $description;
        $bind_types .= 's';
    }
    
    if ($image_url !== null) {
        $update_fields[] = 'image_url = ?';
        $update_values[] = $image_url;
        $bind_types .= 's';
    }
    
    if ($is_long_stock !== null) {
        $update_fields[] = 'is_long_stock = ?';
        $update_values[] = $is_long_stock;
        $bind_types .= 'i';
    }
    
    if ($long_stock_reason !== null) {
        $update_fields[] = 'long_stock_reason = ?';
        $update_values[] = $long_stock_reason;
        $bind_types .= 's';
    }
    
    // Xử lý tự động tạo category từ brand (nếu brand được update)
    if ($brand !== null && $category_id === null) {
        // Tìm hoặc tạo category từ brand
        $stmt_check = $conn->prepare("SELECT id FROM categories WHERE name = ? LIMIT 1");
        $stmt_check->bind_param("s", $brand);
        $stmt_check->execute();
        $check_result = $stmt_check->get_result();
        
        if ($check_result->num_rows > 0) {
            // Brand đã tồn tại trong categories
            $row = $check_result->fetch_assoc();
            $category_id = $row['id'];
        } else {
            // Brand chưa tồn tại → tự động tạo category mới
            $stmt_insert = $conn->prepare("INSERT INTO categories (name, description, is_visible, status) VALUES (?, ?, 1, 1)");
            $description_for_brand = "Dòng xe " . $brand;
            $stmt_insert->bind_param("ss", $brand, $description_for_brand);
            
            if ($stmt_insert->execute()) {
                $category_id = $conn->insert_id;
            }
            $stmt_insert->close();
        }
        $stmt_check->close();
        
        // Thêm category_id vào update fields nếu nó được tạo hoặc tìm thấy
        if ($category_id !== null) {
            $update_fields[] = 'category_id = ?';
            $update_values[] = $category_id;
            $bind_types .= 'i';
        }
    }
    
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
    
    if (!$stmt) {
        throw new Exception($conn->error);
    }
    
    $stmt->bind_param($bind_types, ...$update_values);
    
    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
    }
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Cập nhật sản phẩm thành công'
    ]);
    $stmt->close();
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage()
    ]);
}
?>
