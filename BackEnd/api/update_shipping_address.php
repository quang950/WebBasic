<?php
/**
 * PUT /BackEnd/api/update_shipping_address.php
 * Cập nhật địa chỉ giao hàng
 */
session_start();
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
require_once __DIR__ . '/../config/db_connect.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ']);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    
    $address_id = isset($data['id']) ? intval($data['id']) : 0;
    $user_id = isset($data['user_id']) ? intval($data['user_id']) : 0;
    $recipient_name = isset($data['recipient_name']) ? trim($data['recipient_name']) : '';
    $phone = isset($data['phone']) ? trim($data['phone']) : '';
    $address_detail = isset($data['address_detail']) ? trim($data['address_detail']) : '';
    $ward = isset($data['ward']) ? trim($data['ward']) : '';
    $district = isset($data['district']) ? trim($data['district']) : '';
    $province = isset($data['province']) ? trim($data['province']) : '';
    $postal_code = isset($data['postal_code']) ? trim($data['postal_code']) : '';
    $is_default = isset($data['is_default']) ? intval($data['is_default']) : 0;

    // Validate required fields
    if (!$address_id || !$user_id || !$recipient_name || !$phone || !$address_detail || !$district || !$province) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Vui lòng điền đầy đủ thông tin bắt buộc'
        ]);
        exit;
    }

    // Phone format validation
    if (!preg_match('/^0\d{9,}$/', $phone)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Số điện thoại không hợp lệ'
        ]);
        exit;
    }

    // Kiểm tra địa chỉ tồn tại và thuộc user này không
    $stmt = $conn->prepare("
        SELECT id FROM user_shipping_addresses 
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

    // Nếu set is_default = 1, bỏ flag mặc định từ các địa chỉ khác
    if ($is_default) {
        $stmt = $conn->prepare("
            UPDATE user_shipping_addresses 
            SET is_default = 0 
            WHERE user_id = ? AND id != ? AND is_default = 1
        ");
        $stmt->bind_param("ii", $user_id, $address_id);
        $stmt->execute();
    }

    // Cập nhật địa chỉ
    $stmt = $conn->prepare("
        UPDATE user_shipping_addresses 
        SET recipient_name = ?, phone = ?, address_detail = ?, 
            ward = ?, district = ?, province = ?, postal_code = ?, is_default = ?,
            updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->bind_param("sssssSSii", $recipient_name, $phone, $address_detail, $ward, $district, $province, $postal_code, $is_default, $address_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0 || true) { // Cho phép cập nhật ngay cả khi không có thay đổi
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Cập nhật địa chỉ thành công',
            'data' => [
                'id' => $address_id,
                'recipient_name' => $recipient_name,
                'phone' => $phone,
                'address_detail' => $address_detail,
                'ward' => $ward,
                'district' => $district,
                'province' => $province,
                'postal_code' => $postal_code,
                'is_default' => $is_default
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Không thể cập nhật địa chỉ'
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
