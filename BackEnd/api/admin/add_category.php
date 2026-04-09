<?php
/**
 * Admin API: Add new category
 * POST /BackEnd/api/admin/add_category.php
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
    exit; // No connection to close
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

// Name validation (required, 3-100 characters)
if (empty($data['name'])) {
    $errors['name'] = 'Tên danh mục không được để trống';
} else {
    $nameCheck = validateLength($data['name'], 3, 100, 'Tên danh mục');
    if (!$nameCheck['valid']) {
        $errors['name'] = $nameCheck['message'];
    } else {
        // Check duplicate
        if (categoryNameExists($conn, trim($data['name']))) {
            $errors['name'] = 'Tên danh mục này đã tồn tại';
        }
    }
}

// Description validation (optional, max 500 characters)
if (!empty($data['description'])) {
    if (strlen($data['description']) > 500) {
        $errors['description'] = 'Mô tả tối đa 500 ký tự';
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
$description = sanitizeString(trim($data['description'] ?? ''));

// ========== INSERT CATEGORY ==========
$stmt = $conn->prepare("
    INSERT INTO categories (name, description, is_visible, status, created_at, updated_at)
    VALUES (?, ?, 1, 1, NOW(), NOW())
");

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    $conn->close();
    exit;
}

$stmt->bind_param("ss", $name, $description);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Insert failed: ' . $stmt->error]);
    $stmt->close();
    $conn->close();
    exit;
}

$category_id = $stmt->insert_id;
$stmt->close();

http_response_code(201);
echo json_encode([
    'success' => true,
    'message' => 'Thêm danh mục thành công',
    'category_id' => $category_id
]);

$conn->close();
?>
