<?php
/**
 * DELETE /BackEnd/api/delete_shipping_address.php?id=[id]
 * Xóa địa chỉ giao hàng
 */
session_start();
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
require_once __DIR__ . '/../config/db_connect.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ']);
        exit;
    }

    $address_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

    if (!$address_id || !$user_id) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'id và user_id bắt buộc'
        ]);
        exit;
    }

    // Kiểm tra địa chỉ có tồn tại và thuộc user này không
    $stmt = $conn->prepare("
        SELECT id, is_default FROM user_shipping_addresses 
        WHERE id = ? AND user_id = ?
    ");
    $stmt->bind_param("ii", $address_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Địa chỉ không tồn tại'
        ]);
        exit;
    }

    $address = $result->fetch_assoc();
    $was_default = $address['is_default'];

    // Xóa địa chỉ
    $stmt = $conn->prepare("DELETE FROM user_shipping_addresses WHERE id = ?");
    $stmt->bind_param("i", $address_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Nếu xóa địa chỉ mặc định, đặt địa chỉ đầu tiên là mặc định
        if ($was_default) {
            $stmt = $conn->prepare("
                UPDATE user_shipping_addresses 
                SET is_default = 1 
                WHERE user_id = ? AND is_default = 0 
                ORDER BY created_at DESC 
                LIMIT 1
            ");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
        }

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Xóa địa chỉ thành công'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Không thể xóa địa chỉ'
        ]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi server: ' . $e->getMessage()
    ]);
}
?>
