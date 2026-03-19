<?php
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(json_encode(['success' => true]));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ']);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        $data = $_POST;
    }

    if (!$data) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Không có dữ liệu']);
        exit;
    }

    $userId = isset($data['userId']) ? (int)$data['userId'] : 0;
    $email = trim($data['email'] ?? '');

    if ($userId <= 0 && $email === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Thiếu userId hoặc email hợp lệ']);
        exit;
    }

    require_once '../config/db_connect.php';
    $connection = isset($conn) ? $conn : null;

    require_once '../controllers/UserController.php';
    $controller = new UserController($connection);

    $payload = [
        'email' => $email,
        'firstName' => trim($data['firstName'] ?? ''),
        'lastName' => trim($data['lastName'] ?? ''),
        'phone' => trim($data['phone'] ?? ''),
        'birthDate' => $data['birthDate'] ?? null,
        'province' => trim($data['province'] ?? ''),
        'address' => trim($data['address'] ?? ''),
    ];

    $result = $controller->handleUpdateUser($userId, $payload);

    if (!empty($dbError)) {
        $result['db_warning'] = $dbError;
    }

    echo json_encode($result);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi server: ' . $e->getMessage(),
    ]);
}
?>
