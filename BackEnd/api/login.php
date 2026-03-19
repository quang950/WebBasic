<?php
// login.php - API để đăng nhập tài khoản
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(json_encode(['success' => true]));
}

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ']);
    exit;
}

try {
    // Include files
    require_once '../config/db_connect.php';
    $connection = isset($conn) ? $conn : null;
    
    require_once '../controllers/UserController.php';
    
    // Get JSON data
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!$data) {
        $data = $_POST;
    }
    
    if (!$data) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Không có dữ liệu']);
        exit;
    }
    
    // Create controller and handle login
    $controller = new UserController($connection);
    $result = $controller->handleLogin($data);

    if (!empty($dbError)) {
        $result['db_warning'] = $dbError;
    }
    
    // Return response
    echo json_encode($result);
    
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi server: ' . $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
?>
