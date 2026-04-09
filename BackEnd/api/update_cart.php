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
require_once dirname(__DIR__) . '/controllers/CartController.php';

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

    $cart_id = $data['cart_id'];
    
    // Check if delta or quantity is provided
    if (isset($data['delta'])) {
        // Delta mode: get current quantity + delta
        $delta = (int)$data['delta'];
        
        // Get current quantity from database
        $stmt = $conn->prepare("SELECT quantity FROM cart WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $cart_id, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Cart item không tìm thấy'], JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        $row = $result->fetch_assoc();
        $currentQty = (int)$row['quantity'];
        $newQty = $currentQty + $delta;  // Don't use max() yet - we need to check if should delete
        
        $stmt->close();
    } else if (isset($data['quantity'])) {
        // Quantity mode: use provided quantity
        $newQty = (int)$data['quantity'];
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Thiếu dữ liệu (delta hoặc quantity)'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // If quantity becomes <= 0, delete the item instead of updating
    if ($newQty <= 0) {
        $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $cart_id, $userId);
        if ($stmt->execute()) {
            http_response_code(200);
            echo json_encode(['success' => true, 'message' => 'Sản phẩm đã xóa'], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Lỗi xóa: ' . $stmt->error], JSON_UNESCAPED_UNICODE);
        }
        $stmt->close();
        exit;
    }

    // Otherwise, update with new quantity
    $controller = new CartController($conn);
    $result = $controller->update([
        'cart_id' => $cart_id,
        'quantity' => $newQty
    ]);

    http_response_code(200);
    echo json_encode($result, JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
