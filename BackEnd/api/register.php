<?php
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

// Check required fields
if (empty($data['email'])) {
    $errors['email'] = 'Email không được để trống';
} else {
    $email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email không hợp lệ';
    }
    if (strlen($email) > 100) {
        $errors['email'] = 'Email tối đa 100 ký tự';
    }
}

if (empty($data['password'])) {
    $errors['password'] = 'Mật khẩu không được để trống';
} else {
    $password = $data['password'];
    if (strlen($password) < 6) {
        $errors['password'] = 'Mật khẩu tối thiểu 6 ký tự';
    }
    if (strlen($password) > 255) {
        $errors['password'] = 'Mật khẩu tối đa 255 ký tự';
    }
    // Check password strength
    if (!preg_match('/[a-zA-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors['password'] = 'Mật khẩu phải chứa chữ cái và số';
    }
}

if (empty($data['firstName'])) {
    $errors['firstName'] = 'Họ không được để trống';
} else {
    $firstName = trim($data['firstName']);
    if (strlen($firstName) < 2) {
        $errors['firstName'] = 'Họ tối thiểu 2 ký tự';
    }
    if (strlen($firstName) > 50) {
        $errors['firstName'] = 'Họ tối đa 50 ký tự';
    }
    if (preg_match('/[0-9]/', $firstName)) {
        $errors['firstName'] = 'Họ không được chứa số';
    }
    $firstName = htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8');
}

if (empty($data['lastName'])) {
    $errors['lastName'] = 'Tên không được để trống';
} else {
    $lastName = trim($data['lastName']);
    if (strlen($lastName) < 2) {
        $errors['lastName'] = 'Tên tối thiểu 2 ký tự';
    }
    if (strlen($lastName) > 50) {
        $errors['lastName'] = 'Tên tối đa 50 ký tự';
    }
    if (preg_match('/[0-9]/', $lastName)) {
        $errors['lastName'] = 'Tên không được chứa số';
    }
    $lastName = htmlspecialchars($lastName, ENT_QUOTES, 'UTF-8');
}

// Phone validation (optional but if provided, must be valid)
$phone = htmlspecialchars($data['phone'] ?? '', ENT_QUOTES, 'UTF-8');
if (!empty($phone)) {
    $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
    if (!preg_match('/^0[0-9]{9,10}$/', $cleanPhone)) {
        $errors['phone'] = 'Số điện thoại phải là 10-11 số, bắt đầu 0';
    }
}

// Birth date validation (optional but if provided, must be valid)
$birthDate = htmlspecialchars($data['birthDate'] ?? '', ENT_QUOTES, 'UTF-8');
if (!empty($birthDate)) {
    $bdTime = strtotime($birthDate);
    if ($bdTime === false) {
        $errors['birthDate'] = 'Ngày sinh không hợp lệ';
    } else {
        // Check not in future
        if ($bdTime > time()) {
            $errors['birthDate'] = 'Ngày sinh không được lớn hơn hôm nay';
        }
        // Check >= 18 years old
        $age = date('Y') - date('Y', $bdTime);
        $m = date('m', $bdTime) - date('m');
        $d = date('d', $bdTime) - date('d');
        if ($m < 0 || ($m == 0 && $d < 0)) {
            $age--;
        }
        if ($age < 18) {
            $errors['birthDate'] = 'Bạn phải >= 18 tuổi';
        }
    }
}

// Province validation (optional)
$province = htmlspecialchars($data['province'] ?? '', ENT_QUOTES, 'UTF-8');

// Address validation (optional)
$address = htmlspecialchars($data['address'] ?? '', ENT_QUOTES, 'UTF-8');

// Return errors if any
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

// ========== CHECK DUPLICATE EMAIL ==========
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    http_response_code(409);
    echo json_encode(['success' => false, 'message' => 'Email này đã được đăng ký']);
    $stmt->close();
    exit;
}
$stmt->close();

// ========== HASH PASSWORD & INSERT USER ==========
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

$stmt = $conn->prepare("
    INSERT INTO users (
        email, password, first_name, last_name, 
        phone, birth_date, province, address, 
        is_admin, locked, created_at, updated_at
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, 0, NOW(), NOW())
");

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}

$stmt->bind_param(
    "ssssssss",
    $email, $hashedPassword, $firstName, $lastName,
    $phone, $birthDate, $province, $address
);

if ($stmt->execute()) {
    $userId = $stmt->insert_id;
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Đăng ký thành công',
        'user_id' => $userId
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Đăng ký thất bại: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>
