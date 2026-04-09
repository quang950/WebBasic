<?php
/**
 * API: Add product to cart
 * POST /BackEnd/api/add_to_cart.php
 * Requires: User login
 */
session_start();
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../config/helper.php';

// ========== CHECK CONNECTION ==========
if (!$conn || !$dbConnected) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection error']);
    exit;
}

// ========== OPTIONS REQUEST ==========
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    $conn->close();
    exit;
}

// ========== AUTHORIZATION ==========
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
    $conn->close();
    exit;
}

// ========== VALIDATION ==========
checkPost();

$data = getJsonInput();
$userId = getCurrentUserId();
$errors = [];

// Product ID validation
if (empty($data['product_id'])) {
    $errors['product_id'] = 'ID sản phẩm không được để trống';
} else {
    $productId = intval($data['product_id']);
    if ($productId <= 0) {
        $errors['product_id'] = 'ID sản phẩm không hợp lệ';
    } else if (!recordExists($conn, 'products', $productId)) {
        $errors['product_id'] = 'Sản phẩm không tồn tại';
    }
}

// Quantity validation
if (empty($data['quantity'])) {
    $errors['quantity'] = 'Số lượng không được để trống';
} else {
    $qtyCheck = validateQuantity($data['quantity']);
    if (!$qtyCheck['valid']) {
        $errors['quantity'] = $qtyCheck['message'];
    }
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'errors' => $errors]);
    $conn->close();
    exit;
}

// ========== SANITIZE INPUTS ==========
$productId = intval($data['product_id']);
$quantity = intval($data['quantity']);

// ========== CHECK STOCK ==========
$stmt = $conn->prepare("SELECT stock FROM products WHERE id = ? LIMIT 1");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
    $conn->close();
    exit;
}

$stmt->bind_param("i", $productId);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if (!$product) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại']);
    $conn->close();
    exit;
}

if ($product['stock'] <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Sản phẩm này đã hết hàng']);
    $conn->close();
    exit;
}

if ($quantity > $product['stock']) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Số lượng yêu cầu vượt quá kho hàng']);
    $conn->close();
    exit;
}

// ========== ADD OR UPDATE CART ==========
// Check if product already in cart
$stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
    $conn->close();
    exit;
}

$stmt->bind_param("ii", $userId, $productId);
$stmt->execute();
$result = $stmt->get_result();
$cartItem = $result->fetch_assoc();
$stmt->close();

if ($cartItem) {
    // Update existing cart item
    $newQuantity = $cartItem['quantity'] + $quantity;
    
    // Check new quantity doesn't exceed stock
    if ($newQuantity > $product['stock']) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Tổng số lượng vượt quá kho hàng']);
        $conn->close();
        exit;
    }
    
    $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error']);
        $conn->close();
        exit;
    }
    
    $stmt->bind_param("iii", $newQuantity, $userId, $productId);
    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Cập nhật giỏ hàng thất bại']);
        $conn->close();
        exit;
    }
    $stmt->close();
} else {
    // Insert new cart item
    $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity, added_at) VALUES (?, ?, ?, NOW())");
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error']);
        $conn->close();
        exit;
    }
    
    $stmt->bind_param("iii", $userId, $productId, $quantity);
    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Thêm vào giỏ hàng thất bại']);
        $conn->close();
        exit;
    }
    $stmt->close();
}

http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Thêm vào giỏ hàng thành công'
]);

$conn->close();
?>