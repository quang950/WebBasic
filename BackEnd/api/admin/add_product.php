<?php
/**
 * Admin API: Add new product
 * POST /BackEnd/api/admin/add_product.php
 * Requires: Admin login
 */
session_start();
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../config/db_connect.php';
require_once __DIR__ . '/../../config/helper.php';

// ========== CHECK CONNECTION ==========
if (!$conn || !$dbConnected) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection error']);
    exit;
}

// ========== AUTHORIZATION ==========
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
    $conn->close();
    exit;
}

if (!isAdmin()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Chỉ quản trị viên mới có quyền']);
    $conn->close();
    exit;
}

// ========== VALIDATION ==========
checkPost();

$data = getJsonInput();
$errors = [];

// Validate required fields
$requiredFields = ['name', 'price'];
$requiredErrors = validateRequired($data, $requiredFields);
if (!empty($requiredErrors)) {
    $errors = array_merge($errors, $requiredErrors);
}

// Name validation
if (!empty($data['name'])) {
    $nameCheck = validateLength($data['name'], 3, 255, 'Tên sản phẩm');
    if (!$nameCheck['valid']) {
        $errors['name'] = $nameCheck['message'];
    } else {
        // Check duplicate name
        if (productNameExists($conn, trim($data['name']))) {
            $errors['name'] = 'Tên sản phẩm này đã tồn tại';
        }
    }
}

// Price validation
if (!empty($data['price'])) {
    $priceCheck = validateCurrency($data['price'], 'Giá bán');
    if (!$priceCheck['valid']) {
        $errors['price'] = $priceCheck['message'];
    }
}

// Cost price validation (optional)
if (!empty($data['cost_price'])) {
    $costCheck = validateCurrency($data['cost_price'], 'Giá nhập');
    if (!$costCheck['valid']) {
        $errors['cost_price'] = $costCheck['message'];
    } else if (!empty($data['price'])) {
        // Check cost_price < price
        if (floatval($data['cost_price']) >= floatval($data['price'])) {
            $errors['cost_price'] = 'Giá nhập phải < giá bán';
        }
    }
}

// Stock validation (optional)
if (isset($data['stock']) && $data['stock'] !== '') {
    $stockCheck = validateQuantity($data['stock']);
    if (!$stockCheck['valid']) {
        $errors['stock'] = $stockCheck['message'];
    }
}

// Description validation (optional)
if (!empty($data['description'])) {
    if (strlen($data['description']) > 1000) {
        $errors['description'] = 'Mô tả tối đa 1000 ký tự';
    }
}

// Brand validation (optional)
if (!empty($data['brand'])) {
    if (strlen($data['brand']) > 100) {
        $errors['brand'] = 'Thương hiệu tối đa 100 ký tự';
    }
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'errors' => $errors]);
    $conn->close();
    exit;
}

// ========== SANITIZE INPUTS ==========
$name = sanitizeString(trim($data['name']));
$price = floatval($data['price']);
$cost_price = !empty($data['cost_price']) ? floatval($data['cost_price']) : 0;
$profit_margin = !empty($data['profit_margin']) ? floatval($data['profit_margin']) : 10;
$stock = !empty($data['stock']) ? intval($data['stock']) : 0;
$brand = sanitizeString(trim($data['brand'] ?? ''));
$category = sanitizeString(trim($data['category'] ?? ''));
$description = sanitizeString(trim($data['description'] ?? ''));
$image_url = sanitizeString(trim($data['image_url'] ?? ''));

// ========== GET CATEGORY ID ==========
$category_id = null;

// Priority 1: Find by category name if provided
if (!empty($category)) {
    $stmt = $conn->prepare("SELECT id FROM categories WHERE name = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param("s", $category);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $category_id = $row['id'];
        }
        $stmt->close();
    }
}

// Priority 2: Find or create category from brand
if ($category_id === null && !empty($brand)) {
    $stmt = $conn->prepare("SELECT id FROM categories WHERE name = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param("s", $brand);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $category_id = $row['id'];
        } else {
            // Create new category from brand
            $desc = "Dòng xe " . $brand;
            $stmt2 = $conn->prepare("INSERT INTO categories (name, description, is_visible, status) VALUES (?, ?, 1, 1)");
            if ($stmt2) {
                $stmt2->bind_param("ss", $brand, $desc);
                if ($stmt2->execute()) {
                    $category_id = $conn->insert_id;
                }
                $stmt2->close();
            }
        }
        $stmt->close();
    }
}

// Priority 3: Use first category
if ($category_id === null) {
    $stmt = $conn->prepare("SELECT id FROM categories LIMIT 1");
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $category_id = $row['id'];
        }
        $stmt->close();
    }
}

// Fallback
if ($category_id === null) {
    $category_id = 1;
}

// ========== INSERT PRODUCT ==========
$stmt = $conn->prepare("
    INSERT INTO products 
    (name, brand, category_id, price, cost_price, profit_margin, stock, description, image_url, created_at, updated_at)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
");

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    $conn->close();
    exit;
}

$stmt->bind_param(
    "ssidddiss",
    $name, $brand, $category_id, $price, $cost_price, $profit_margin, $stock, $description, $image_url
);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Insert failed: ' . $stmt->error]);
    $stmt->close();
    $conn->close();
    exit;
}

$product_id = $stmt->insert_id;
$stmt->close();

http_response_code(201);
echo json_encode([
    'success' => true,
    'message' => 'Thêm sản phẩm thành công',
    'product_id' => $product_id
]);

$conn->close();
?>

