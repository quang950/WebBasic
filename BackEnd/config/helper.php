<?php
/**
 * ====================================
 * Helper Functions for API Validation & Authorization
 * ====================================
 */

// ========== SESSION & AUTHORIZATION ==========

/**
 * Kiểm tra user đã đăng nhập
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Kiểm tra user là admin
 * @return bool
 */
function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}

/**
 * Lấy user_id từ session
 * @return int|null
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Require user đã đăng nhập (dùng cho user endpoints)
 * @param $message string - Thông báo lỗi tùy chỉnh
 */
function requireLogin($message = 'Vui lòng đăng nhập') {
    if (!isLoggedIn()) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => $message]);
        exit;
    }
}

/**
 * Require admin (dùng cho admin endpoints)
 * @param $message string - Thông báo lỗi tùy chỉnh
 */
function requireAdmin($message = 'Chỉ quản trị viên mới có quyền') {
    if (!isAdmin()) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => $message]);
        exit;
    }
}

/**
 * Require cả login và admin
 * @param $message string - Thông báo lỗi tùy chỉnh
 */
function requireAdminLogin($message = 'Vui lòng đăng nhập với tài khoản quản trị viên') {
    requireLogin();
    requireAdmin($message);
}

// ========== REQUEST VALIDATION ==========

/**
 * Kiểm tra request method
 * @param $method string - GET|POST|PUT|DELETE
 * @return void
 */
function checkMethod($method) {
    if ($_SERVER['REQUEST_METHOD'] !== $method) {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        exit;
    }
}

/**
 * Kiểm tra POST request
 * @return void
 */
function checkPost() {
    checkMethod('POST');
}

/**
 * Kiểm tra GET request
 * @return void
 */
function checkGet() {
    checkMethod('GET');
}

/**
 * Lấy JSON input data
 * @return array
 */
function getJsonInput() {
    $data = json_decode(file_get_contents('php://input'), true);
    return is_array($data) ? $data : [];
}

/**
 * Kiểm tra required fields có tồn tại
 * @param $data array - Dữ liệu input
 * @param $fields array - Danh sách các field cần kiểm tra
 * @return array - Danh sách error nếu có
 */
function validateRequired($data, $fields) {
    $errors = [];
    foreach ($fields as $field) {
        if (empty($data[$field])) {
            $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' không được để trống';
        }
    }
    return $errors;
}

// ========== INPUT SANITIZATION ==========

/**
 * Sanitize string input
 * @param $str string - Chuỗi cần sanitize
 * @param $trim bool - Có xóa khoảng trắng đầu cuối
 * @return string
 */
function sanitizeString($str, $trim = true) {
    $str = htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    return $trim ? trim($str) : $str;
}

/**
 * Sanitize email
 * @param $email string
 * @return string
 */
function sanitizeEmail($email) {
    return filter_var($email, FILTER_SANITIZE_EMAIL);
}

/**
 * Sanitize number
 * @param $num string|int|float
 * @return float|int
 */
function sanitizeNumber($num) {
    return filter_var($num, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
}

/**
 * Sanitize integer
 * @param $num string|int
 * @return int
 */
function sanitizeInt($num) {
    return filter_var($num, FILTER_SANITIZE_NUMBER_INT);
}

// ========== VALIDATION RULES ==========

/**
 * Validate email format
 * @param $email string
 * @return array ['valid' => bool, 'message' => string]
 */
function validateEmail($email) {
    if (empty($email)) {
        return ['valid' => false, 'message' => 'Email không được để trống'];
    }
    
    $email = sanitizeEmail($email);
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['valid' => false, 'message' => 'Email không hợp lệ'];
    }
    
    if (strlen($email) > 100) {
        return ['valid' => false, 'message' => 'Email tối đa 100 ký tự'];
    }
    
    return ['valid' => true, 'message' => ''];
}

/**
 * Validate password
 * @param $password string
 * @param $checkStrength bool - Kiểm tra độ mạnh
 * @return array
 */
function validatePassword($password, $checkStrength = false) {
    if (empty($password)) {
        return ['valid' => false, 'message' => 'Mật khẩu không được để trống'];
    }
    
    if (strlen($password) < 6) {
        return ['valid' => false, 'message' => 'Mật khẩu tối thiểu 6 ký tự'];
    }
    
    if (strlen($password) > 255) {
        return ['valid' => false, 'message' => 'Mật khẩu tối đa 255 ký tự'];
    }
    
    if ($checkStrength) {
        if (!preg_match('/[a-zA-Z]/', $password)) {
            return ['valid' => false, 'message' => 'Mật khẩu phải chứa chữ cái'];
        }
        if (!preg_match('/[0-9]/', $password)) {
            return ['valid' => false, 'message' => 'Mật khẩu phải chứa số'];
        }
    }
    
    return ['valid' => true, 'message' => ''];
}

/**
 * Validate phone number
 * @param $phone string
 * @return array
 */
function validatePhone($phone) {
    if (empty($phone)) {
        return ['valid' => false, 'message' => 'Số điện thoại không được để trống'];
    }
    
    $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
    
    if (!preg_match('/^0[0-9]{9,10}$/', $cleanPhone)) {
        return ['valid' => false, 'message' => 'Số điện thoại phải là 10-11 số, bắt đầu 0'];
    }
    
    return ['valid' => true, 'message' => ''];
}

/**
 * Validate currency (positive number)
 * @param $amount mixed
 * @param $fieldName string
 * @return array
 */
function validateCurrency($amount, $fieldName = 'Giá') {
    if (empty($amount) && $amount !== '0' && $amount !== 0) {
        return ['valid' => false, 'message' => $fieldName . ' không được để trống'];
    }
    
    $numAmount = floatval($amount);
    
    if ($numAmount < 0) {
        return ['valid' => false, 'message' => $fieldName . ' không được âm'];
    }
    
    if ($numAmount > 999999999999) {
        return ['valid' => false, 'message' => $fieldName . ' quá lớn'];
    }
    
    return ['valid' => true, 'message' => ''];
}

/**
 * Validate quantity (positive integer)
 * @param $quantity mixed
 * @param $maxQuantity int|null
 * @return array
 */
function validateQuantity($quantity, $maxQuantity = null) {
    if (empty($quantity) && $quantity !== '0' && $quantity !== 0) {
        return ['valid' => false, 'message' => 'Số lượng không được để trống'];
    }
    
    $numQuantity = intval($quantity);
    
    if ($numQuantity < 0) {
        return ['valid' => false, 'message' => 'Số lượng không được âm'];
    }
    
    if ($maxQuantity !== null && $numQuantity > $maxQuantity) {
        return ['valid' => false, 'message' => 'Số lượng tối đa ' . $maxQuantity];
    }
    
    return ['valid' => true, 'message' => ''];
}

/**
 * Validate string length
 * @param $str string
 * @param $minLength int
 * @param $maxLength int
 * @param $fieldName string
 * @return array
 */
function validateLength($str, $minLength, $maxLength, $fieldName = 'Nội dung') {
    if (empty($str)) {
        return ['valid' => false, 'message' => $fieldName . ' không được để trống'];
    }
    
    $str = trim($str);
    $length = strlen($str);
    
    if ($length < $minLength) {
        return ['valid' => false, 'message' => $fieldName . ' tối thiểu ' . $minLength . ' ký tự'];
    }
    
    if ($length > $maxLength) {
        return ['valid' => false, 'message' => $fieldName . ' tối đa ' . $maxLength . ' ký tự'];
    }
    
    return ['valid' => true, 'message' => ''];
}

/**
 * Validate date format
 * @param $dateStr string - Format: YYYY-MM-DD
 * @param $minAge int|null - Tuổi tối thiểu
 * @return array
 */
function validateDate($dateStr, $minAge = null) {
    if (empty($dateStr)) {
        return ['valid' => false, 'message' => 'Ngày không được để trống'];
    }
    
    $date = DateTime::createFromFormat('Y-m-d', $dateStr);
    if (!$date || $date->format('Y-m-d') !== $dateStr) {
        return ['valid' => false, 'message' => 'Ngày không hợp lệ'];
    }
    
    // Check not in future
    if ($date > new DateTime()) {
        return ['valid' => false, 'message' => 'Ngày không được lớn hơn hôm nay'];
    }
    
    // Check min age
    if ($minAge !== null) {
        $age = (new DateTime())->diff($date)->y;
        if ($age < $minAge) {
            return ['valid' => false, 'message' => 'Bạn phải >= ' . $minAge . ' tuổi'];
        }
    }
    
    return ['valid' => true, 'message' => ''];
}

/**
 * Validate URL format
 * @param $url string
 * @return array
 */
function validateUrl($url) {
    if (empty($url)) {
        return ['valid' => false, 'message' => 'URL không được để trống'];
    }
    
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return ['valid' => false, 'message' => 'URL không hợp lệ'];
    }
    
    return ['valid' => true, 'message' => ''];
}

// ========== ERROR RESPONSE ==========

/**
 * Trả về error response với HTTP code
 * @param $message string - Thông báo lỗi
 * @param $errors array|null - Chi tiết lỗi
 * @param $statusCode int - HTTP status code
 */
function errorResponse($message, $errors = null, $statusCode = 400) {
    http_response_code($statusCode);
    $response = [
        'success' => false,
        'message' => $message
    ];
    if ($errors) {
        $response['errors'] = $errors;
    }
    echo json_encode($response);
    exit;
}

/**
 * Trả về success response
 * @param $message string - Thông báo thành công
 * @param $data array|null - Dữ liệu trả về
 * @param $statusCode int - HTTP status code
 */
function successResponse($message, $data = null, $statusCode = 200) {
    http_response_code($statusCode);
    $response = [
        'success' => true,
        'message' => $message
    ];
    if ($data) {
        $response['data'] = $data;
    }
    echo json_encode($response);
    exit;
}

// ========== DATABASE HELPERS ==========

/**
 * Kiểm tra record có tồn tại
 * @param $conn mysqli - Database connection
 * @param $table string - Tên bảng
 * @param $id int - ID của record
 * @return bool
 */
function recordExists($conn, $table, $id) {
    $stmt = $conn->prepare("SELECT id FROM $table WHERE id = ? LIMIT 1");
    if (!$stmt) return false;
    
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $exists = $result->num_rows > 0;
    $stmt->close();
    
    return $exists;
}

/**
 * Kiểm tra email đã tồn tại (ngoài trừ user hiện tại)
 * @param $conn mysqli
 * @param $email string
 * @param $excludeUserId int|null
 * @return bool
 */
function emailExists($conn, $email, $excludeUserId = null) {
    $query = "SELECT id FROM users WHERE email = ?";
    $params = ["s", $email];
    
    if ($excludeUserId !== null) {
        $query .= " AND id != ?";
        $params = ["si", $email, $excludeUserId];
    }
    
    $stmt = $conn->prepare($query);
    if (!$stmt) return true; // Giả định tồn tại nếu lỗi
    
    call_user_func_array([$stmt, 'bind_param'], $params);
    $stmt->execute();
    $result = $stmt->get_result();
    $exists = $result->num_rows > 0;
    $stmt->close();
    
    return $exists;
}

/**
 * Kiểm tra product name đã tồn tại (ngoài trừ product hiện tại)
 * @param $conn mysqli
 * @param $name string
 * @param $excludeProductId int|null
 * @return bool
 */
function productNameExists($conn, $name, $excludeProductId = null) {
    $query = "SELECT id FROM products WHERE name = ?";
    $params = ["s", $name];
    
    if ($excludeProductId !== null) {
        $query .= " AND id != ?";
        $params = ["si", $name, $excludeProductId];
    }
    
    $stmt = $conn->prepare($query);
    if (!$stmt) return true;
    
    call_user_func_array([$stmt, 'bind_param'], $params);
    $stmt->execute();
    $result = $stmt->get_result();
    $exists = $result->num_rows > 0;
    $stmt->close();
    
    return $exists;
}

/**
 * Kiểm tra category name đã tồn tại (ngoài trừ category hiện tại)
 * @param $conn mysqli
 * @param $name string
 * @param $excludeCategoryId int|null
 * @return bool
 */
function categoryNameExists($conn, $name, $excludeCategoryId = null) {
    $query = "SELECT id FROM categories WHERE name = ?";
    $params = ["s", $name];
    
    if ($excludeCategoryId !== null) {
        $query .= " AND id != ?";
        $params = ["si", $name, $excludeCategoryId];
    }
    
    $stmt = $conn->prepare($query);
    if (!$stmt) return true;
    
    call_user_func_array([$stmt, 'bind_param'], $params);
    $stmt->execute();
    $result = $stmt->get_result();
    $exists = $result->num_rows > 0;
    $stmt->close();
    
    return $exists;
}

?>