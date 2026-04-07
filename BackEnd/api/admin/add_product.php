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
    
    // Get category_id from category name
    $category_id = 1; // Default to first category
    if (!empty($category)) {
        $stmt = $conn->prepare("SELECT id FROM categories WHERE name = ? LIMIT 1");
        $stmt->bind_param("s", $category);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $category_id = $row['id'];
        }
    }
    
    // Always use first category if not found
    if ($category_id === 1) {
        $stmt = $conn->prepare("SELECT id FROM categories LIMIT 1");
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $category_id = $row['id'];
        }
    }
    
    // Insert product
    $stmt = $conn->prepare("
        INSERT INTO products 
        (name, brand, category_id, price, cost_price, profit_margin, stock, description, image_url)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param(
        "ssidddiss",
        $name, $brand, $category_id, $price, $cost_price, $profit_margin, $stock, $description, $image_url
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

