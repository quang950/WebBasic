<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/db_connect.php';

if (!$conn) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection error']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

// ========== VALIDATION INPUT ==========
$errors = [];

// Email/Username validation
if (empty($data['email'])) {
    $errors['email'] = 'Email hoặc tên đăng nhập không được để trống';
} else {
    $email = trim($data['email']);
    // Cho phép cả email và username (để login với "admin" hoặc "admin@example.com")
    if (strlen($email) < 2) {
        $errors['email'] = 'Email/Tên đăng nhập quá ngắn';
    }
}

// Password validation
if (empty($data['password'])) {
    $errors['password'] = 'Mật khẩu không được để trống';
} else {
    $password = $data['password'];
    if (strlen($password) < 6) {
        $errors['password'] = 'Mật khẩu tối thiểu 6 ký tự';
    }
}

// Return errors if any
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

// ========== QUERY DATABASE ==========
$stmt = $conn->prepare("
    SELECT id, email, password, first_name, last_name, 
           phone, province, address, is_admin, locked 
    FROM users 
    WHERE email = ?
");

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// User not found
if ($result->num_rows === 0) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Email hoặc mật khẩu không chính xác']);
    $stmt->close();
    exit;
}

$user = $result->fetch_assoc();
$stmt->close();

// ========== CHECK ACCOUNT STATUS ==========
if ($user['locked']) {
    http_response_code(403);
    echo json_encode([
        'success' => false, 
        'message' => 'Tài khoản bị khóa. Vui lòng liên hệ quản trị viên để được hỗ trợ'
    ]);
    exit;
}

// ========== VERIFY PASSWORD ==========
if (!password_verify($password, $user['password'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Email hoặc mật khẩu không chính xác']);
    exit;
}

// ========== LOGIN SUCCESS - SET SESSION ==========
$_SESSION['user_id'] = $user['id'];
$_SESSION['email'] = $user['email'];
$_SESSION['is_admin'] = (bool)$user['is_admin'];

http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Đăng nhập thành công',
    'user' => [
        'id' => $user['id'],
        'email' => $user['email'],
        'firstName' => $user['first_name'],
        'lastName' => $user['last_name'],
        'name' => $user['first_name'] . ' ' . $user['last_name'],
        'phone' => $user['phone'],
        'province' => $user['province'],
        'address' => $user['address'],
        'isAdmin' => (bool)$user['is_admin']
    ]
]);

$conn->close();
?>
