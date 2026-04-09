<?php
/**
 * PUT /BackEnd/api/admin/complete_import.php
 * Hoàn thành phiếu nhập hàng (cập nhật stock & cost_price)
 */
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

session_start();
require_once __DIR__ . '/../../config/db_connect.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ']);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    $ticket_id = isset($data['ticket_id']) ? intval($data['ticket_id']) : 0;

    if (!$ticket_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ticket_id bắt buộc']);
        exit;
    }

    // Check ticket exists
    $stmt = $conn->prepare("SELECT id, completed_at FROM import_tickets WHERE id = ?");
    $stmt->bind_param("i", $ticket_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Phiếu nhập không tồn tại']);
        exit;
    }

    $ticket = $result->fetch_assoc();
    if ($ticket['completed_at']) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Phiếu nhập đã được hoàn thành']);
        exit;
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Get all items
        $istmt = $conn->prepare("
            SELECT product_id, quantity, import_price
            FROM import_items
            WHERE import_ticket_id = ?
        ");
        $istmt->bind_param("i", $ticket_id);
        $istmt->execute();
        $iresult = $istmt->get_result();

        while ($item = $iresult->fetch_assoc()) {
            // Update product stock and cost_price
            $ustmt = $conn->prepare("
                UPDATE products 
                SET stock = stock + ?, 
                    cost_price = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");
            $ustmt->bind_param("idi", $item['quantity'], $item['import_price'], $item['product_id']);
            $ustmt->execute();

            // Record stock history
            $hstmt = $conn->prepare("
                INSERT INTO stock_history (product_id, type, quantity, reason)
                VALUES (?, 'import', ?, ?)
            ");
            $reason = "Import Ticket #$ticket_id";
            $hstmt->bind_param("iis", $item['product_id'], $item['quantity'], $reason);
            $hstmt->execute();
        }

        // Mark ticket as completed
        $ustmt = $conn->prepare("
            UPDATE import_tickets 
            SET completed_at = NOW()
            WHERE id = ?
        ");
        $ustmt->bind_param("i", $ticket_id);
        $ustmt->execute();

        // Commit
        $conn->commit();

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Hoàn thành phiếu nhập thành công. Stock và cost_price đã được cập nhật.'
        ]);

    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage()
    ]);
}
?>
