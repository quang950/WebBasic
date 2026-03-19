<?php
// check_session.php - API kiểm tra session đăng nhập
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/db_connect.php';

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(json_encode(['success' => true]));
}

// Check if user session exists in frontend (sent via JSON)
$data = json_decode(file_get_contents("php://input"), true) ?: $_POST;

if (empty($data['email'])) {
    http_response_code(401);
    die(json_encode(['loggedIn' => false, 'message' => 'Không có session']));
}

// Verify email exists in database
$query = "SELECT id, first_name, last_name, email, is_admin FROM users WHERE email = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    http_response_code(500);
    die(json_encode(['loggedIn' => false, 'message' => 'Lỗi server']));
}

$stmt->bind_param("s", $data['email']);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    http_response_code(200);
    echo json_encode([
        'loggedIn' => true,
        'user' => [
            'id' => $user['id'],
            'firstName' => $user['first_name'],
            'lastName' => $user['last_name'],
            'email' => $user['email'],
            'isAdmin' => $user['is_admin']
        ]
    ]);
} else {
    http_response_code(401);
    echo json_encode(['loggedIn' => false, 'message' => 'User không tồn tại']);
}
?>
