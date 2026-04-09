<?php
// cart.php - API giỏ hàng

session_start();

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// OPTIONS (CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
	http_response_code(200);
	echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);
}

// chỉ cho GET + POST
if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'])) {
	http_response_code(405);
	echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ'], JSON_UNESCAPED_UNICODE);
}

require_once dirname(__DIR__) . '/config/db_connect.php';
require_once dirname(__DIR__) . '/controllers/CartController.php';

try {
	if (!$conn) {
		http_response_code(500);
		echo json_encode(['success' => false, 'message' => 'Database connection error']);
		exit;
	}
	
	// FIX giống product
	$action = strtolower(trim($_GET['action'] ?? ''));

	$controller = new CartController($conn ?? null);

	// đọc body JSON
	$input = json_decode(file_get_contents("php://input"), true);

	switch ($action) {

		case 'get':
			$userId = $_SESSION['user_id'] ?? null;
			if (!$userId) {
				throw new Exception("Vui lòng đăng nhập");
			}
			$result = $controller->get((int)$userId);
			break;

		case 'add':
			if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
				throw new Exception("Phải dùng POST");
			}
			$result = $controller->add($input ?? []);
			break;

		case 'update':
			if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
				throw new Exception("Phải dùng POST");
			}
			$result = $controller->update($input ?? []);
			break;

		default:
			throw new Exception("Action không hợp lệ");
	}

	// giống product
	if (!empty($dbError)) {
		$result['db_warning'] = $dbError;
	}

	http_response_code(200);
	echo json_encode($result, JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
	http_response_code(400);
	echo json_encode([
		'success' => false,
		'message' => $e->getMessage()
	], JSON_UNESCAPED_UNICODE);
}