<?php
session_start();
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ'], JSON_UNESCAPED_UNICODE);
    exit;
}

require_once dirname(__DIR__) . '/config/db_connect.php';

try {
    // Kiểm tra user đã đăng nhập
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Lấy dữ liệu
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!$data || !isset($data['cart_id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Thiếu cart_id'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $cartId = (int)$data['cart_id'];

    // Chuẩn bị câu DELETE
    $sql = "DELETE FROM cart WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'SQL lỗi: ' . $conn->error], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $stmt->bind_param("ii", $cartId, $userId);
    
    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Execute lỗi: ' . $stmt->error], JSON_UNESCAPED_UNICODE);
        exit;
    }

    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Sản phẩm đã xóa khỏi giỏ hàng'], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
