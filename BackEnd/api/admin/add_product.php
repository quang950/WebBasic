<?php
/**
 * Admin API: Add new product
 * POST /BackEnd/api/admin/add_product.php
 */
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/db_connect.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
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
    $product_code = trim($data['product_code'] ?? '');
    $price_cost = floatval($data['price_cost'] ?? 0);
    $profit_margin = floatval($data['profit_margin'] ?? 10);
    $stock = intval($data['stock'] ?? 0);
    $initial_stock = intval($data['initial_stock'] ?? $stock);
    $unit = trim($data['unit'] ?? 'chiếc');
    $status = isset($data['status']) ? intval($data['status']) : 1;
    
    $brand = trim($data['brand'] ?? '');
    $year = intval($data['year'] ?? date('Y'));
    $fuel = trim($data['fuel'] ?? '');
    $transmission = trim($data['transmission'] ?? '');
    $category = trim($data['category'] ?? '');
    $description = trim($data['description'] ?? '');
    $image = trim($data['image'] ?? '');
    
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
    
    // Insert product with all new fields
    $stmt = $conn->prepare("
        INSERT INTO products 
        (name, product_code, category_id, price, price_cost, profit_margin, 
         stock, initial_stock, unit, description, image_url, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param(
        "ssidddiiissi",
        $name, $product_code, $category_id, $price, $price_cost, $profit_margin,
        $stock, $initial_stock, $unit, $description, $image, $status
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

