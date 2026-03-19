<?php
// search.php - Tìm kiếm nâng cao theo tên + phân loại + khoảng giá
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
	http_response_code(200);
	echo json_encode(['success' => true]);
	exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
	http_response_code(405);
	echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ']);
	exit;
}

require_once '../config/db_connect.php';
require_once '../controllers/ProductController.php';

try {
	$controller = new ProductController($conn ?? null);
	$result = $controller->handleSearch($_GET);

	if (!empty($dbError)) {
		$result['db_warning'] = $dbError;
	}

	http_response_code($result['success'] ? 200 : 400);
	echo json_encode($result);
} catch (Throwable $e) {
	http_response_code(500);
	echo json_encode([
		'success' => false,
		'message' => 'Lỗi server: ' . $e->getMessage()
	]);
}
