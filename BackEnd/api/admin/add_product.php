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
            'message' => 'Missing required fields'
        ]);
        exit;
    }
    
    // Sanitize inputs
    $name = trim($data['name']);
    $price = floatval($data['price']);
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
    $stmt = $conn->prepare("SELECT id FROM categories LIMIT 1");
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $category_id = $row['id'];
    }
    
    // Insert product
    $stmt = $conn->prepare("
        INSERT INTO products (name, category_id, price, description, image_url, stock)
        VALUES (?, ?, ?, ?, ?, 10)
    ");
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("sids", $name, $category_id, $price, $description, $image);
    
    if (!$stmt->execute()) {
        throw new Exception("Insert failed: " . $stmt->error);
    }
    
    $product_id = $conn->insert_id;
    
    echo json_encode([
        'success' => true,
        'product_id' => $product_id,
        'message' => 'Product added successfully'
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
