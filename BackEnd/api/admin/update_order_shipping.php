<?php
/**
 * Admin API: Cập nhật thông tin giao hàng của đơn hàng
 * POST /BackEnd/api/admin/update_order_shipping.php
 */
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../config/db_connect.php';

// Kiểm tra quyền Admin
$isAdmin = true;
if (isset($_SESSION['user_id']) && isset($_SESSION['is_admin'])) {
    $isAdmin = ($_SESSION['is_admin'] == 1);
}

if (!$isAdmin) {
    http_response_code(403);
    echo json_encode([
        'status' => 'error',
        'message' => 'Bạn không có quyền thực hiện hành động này.',
        'data' => null
    ]);
    exit;
}

try {
    // Nhận dữ liệu POST (JSON)
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, true);

    $orderId = intval($input['order_id'] ?? 0);
    $recipientName = $input['recipient_name'] ?? '';
    $recipientPhone = $input['recipient_phone'] ?? '';
    $shippingAddress = $input['shipping_address'] ?? '';

    if (!$orderId) {
        throw new Exception("Thiếu order_id.");
    }

    if (!$conn) {
        throw new Exception("Database connection failed");
    }
    
    // Kiểm tra column shipping_name tồn tại, nếu không thì thêm
    $checkColQuery = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
                      WHERE TABLE_NAME = 'orders' AND COLUMN_NAME = 'shipping_name' AND TABLE_SCHEMA = DATABASE()";
    $colResult = $conn->query($checkColQuery);
    
    if ($colResult && $colResult->num_rows === 0) {
        // Thêm column nếu chưa tồn tại
        $alterQuery = "ALTER TABLE orders ADD COLUMN shipping_name VARCHAR(255) DEFAULT NULL AFTER shipping_phone";
        if (!$conn->query($alterQuery)) {
            // Log error nhưng không dừng lại - có thể column đã tồn tại
            error_log("ALTER TABLE warning: " . $conn->error);
        }
    }
    
    // Kiểm tra đơn hàng tồn tại
    $checkStmt = $conn->prepare("SELECT id FROM orders WHERE id = ?");
    $checkStmt->bind_param("i", $orderId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows === 0) {
        throw new Exception("Đơn hàng không tồn tại.");
    }
    $checkStmt->close();

    // Cập nhật thông tin giao hàng
    $updateFields = [];
    $types = "i";
    $bindParams = [$orderId];
    
    if (!empty($recipientName)) {
        $updateFields[] = "shipping_name = ?";
        $types .= "s";
        $bindParams[] = $recipientName;
    }
    
    if (!empty($recipientPhone)) {
        $updateFields[] = "shipping_phone = ?";
        $types .= "s";
        $bindParams[] = $recipientPhone;
    }
    
    if (!empty($shippingAddress)) {
        $updateFields[] = "shipping_address = ?";
        $types .= "s";
        $bindParams[] = $shippingAddress;
    }

    if (empty($updateFields)) {
        throw new Exception("Không có dữ liệu để cập nhật.");
    }

    $updateSQL = "UPDATE orders SET " . implode(", ", $updateFields) . " WHERE id = ?";
    
    $stmt = $conn->prepare($updateSQL);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param($types, ...$bindParams);
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $stmt->close();

    echo json_encode([
        'status' => 'success',
        'message' => 'Cập nhật thông tin giao hàng thành công',
        'data' => [
            'order_id' => $orderId,
            'recipient_name' => $recipientName,
            'recipient_phone' => $recipientPhone,
            'shipping_address' => $shippingAddress
        ]
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'data' => null
    ]);
}
?>

