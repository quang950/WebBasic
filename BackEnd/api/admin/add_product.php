<?php
/**
 * Admin API: Add new product
 * POST /BackEnd/api/admin/add_product.php
 */
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Phương thức không được hỗ trợ. Chỉ chấp nhận POST request.'
    ]);
    exit;
}

require_once __DIR__ . '/../../config/db_connect.php';

// Check database connection
if (!$conn || !$dbConnected) {
    http_response_code(503);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi kết nối cơ sở dữ liệu: ' . ($dbError ?? 'Unknown error')
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
    
    // Validate required fields
    if (!isset($data['name']) || !isset($data['price'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Tên sản phẩm và giá bán là bắt buộc'
        ]);
        exit;
    }
    
    // Sanitize inputs
    $name = trim($data['name']);
    $price = floatval($data['price']);
    $cost_price = floatval($data['cost_price'] ?? 0);
    $profit_margin = floatval($data['profit_margin'] ?? 10);
    $stock = intval($data['stock'] ?? 0);
    $brand = trim($data['brand'] ?? '');
    $category = trim($data['category'] ?? '');
    $description = trim($data['description'] ?? '');
    $image_url = trim($data['image_url'] ?? '');
    $is_long_stock = intval($data['is_long_stock'] ?? 0);
    $long_stock_reason = trim($data['long_stock_reason'] ?? '');
    
    // Get category_id from brand (tự động thêm category nếu brand mới)
    $category_id = null;
    
    // Ưu tiên: Tìm category từ tên category được submit
    if (!empty($category)) {
        $stmt = $conn->prepare("SELECT id FROM categories WHERE name = ? LIMIT 1");
        $stmt->bind_param("s", $category);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $category_id = $row['id'];
        }
    }
    
    // Nếu không tìm được category → Tìm hoặc tạo category từ brand
    if ($category_id === null && !empty($brand)) {
        $stmt = $conn->prepare("SELECT id FROM categories WHERE name = ? LIMIT 1");
        $stmt->bind_param("s", $brand);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            // Brand đã tồn tại trong categories
            $category_id = $row['id'];
        } else {
            // Brand chưa tồn tại → tự động tạo category mới
            $stmt = $conn->prepare("INSERT INTO categories (name, description, is_visible, status) VALUES (?, ?, 1, 1)");
            $description_for_brand = "Dòng xe " . $brand;
            $stmt->bind_param("ss", $brand, $description_for_brand);
            
            if ($stmt->execute()) {
                $category_id = $conn->insert_id;
            }
        }
    }
    
    // Nếu vẫn không có category_id → dùng category đầu tiên
    if ($category_id === null) {
        $stmt = $conn->prepare("SELECT id FROM categories LIMIT 1");
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $category_id = $row['id'];
        } else {
            // Nếu không có category nào → dùng 1 (fallback)
            $category_id = 1;
        }
    }
    
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
    
    // Insert product
    $stmt = $conn->prepare("
        INSERT INTO products 
        (name, brand, category_id, price, cost_price, profit_margin, stock, description, image_url, is_long_stock, long_stock_reason)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param(
        "ssidddissis",
        $name, $brand, $category_id, $price, $cost_price, $profit_margin, $stock, $description, $image_url, $is_long_stock, $long_stock_reason
    );
    
    if (!$stmt->execute()) {
        throw new Exception("Insert failed: " . $stmt->error);
    }
    
    $product_id = $conn->insert_id;
    
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'product_id' => $product_id,
        'message' => 'Thêm sản phẩm thành công'
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>

