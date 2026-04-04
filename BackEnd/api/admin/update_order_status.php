<?php
/**
 * Admin API: Cập nhật trạng thái đơn hàng
 * POST /BackEnd/api/admin/update_order_status.php
 */
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../config/db_connect.php';

// Kiểm tra quyền Admin (Theo logic của project)
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

    $orderId = $input['order_id'] ?? null;
    $newStatus = $input['status'] ?? null;

    if (!$orderId || !$newStatus) {
        throw new Exception("Thiếu order_id hoặc trạng thái mới.");
    }

    $allowedStatuses = ['new', 'processing', 'delivered', 'cancelled'];
    if (!in_array($newStatus, $allowedStatuses)) {
        throw new Exception("Trạng thái không hợp lệ.");
    }

    // UPDATE BẰNG PDO (Theo rule.md)
    if (!$pdo) {
        throw new Exception("Database connection failed");
    }
    
    $stmt = $pdo->prepare("UPDATE orders SET status = :status WHERE id = :id");
    $stmt->execute([
        ':status' => $newStatus,
        ':id' => $orderId
    ]);

    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Cập nhật trạng thái đơn hàng thành công.',
            'data' => [
                'order_id' => $orderId,
                'new_status' => $newStatus
            ]
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Không tìm thấy đơn hàng hoặc trạng thái không thay đổi.',
            'data' => null
        ]);
    }

} catch (Exception $e) {
    http_response_code(500);
    // Log lỗi chi tiết nếu cần ở server
    // error_log($e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Đã xảy ra lỗi trên máy chủ. Chi tiết: ' . $e->getMessage(),
        'data' => null
    ]);
}
?>