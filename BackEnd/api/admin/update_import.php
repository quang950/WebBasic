<?php
/**
 * PUT /BackEnd/api/admin/update_import.php
 * Update import ticket (chỉ có thể sửa trước khi hoàn thành)
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
    if (!$conn) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database connection error']);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ']);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    $ticket_id = isset($data['ticket_id']) ? intval($data['ticket_id']) : 0;
    $import_items = $data['items'] ?? [];

    if (!$ticket_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ticket_id bắt buộc']);
        exit;
    }

    // Check ticket exists and not completed
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
        echo json_encode(['success' => false, 'message' => 'Phiếu nhập đã được hoàn thành, không thể sửa']);
        exit;
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Clear old import items
        $deleteStmt = $conn->prepare("DELETE FROM import_items WHERE import_ticket_id = ?");
        $deleteStmt->bind_param("i", $ticket_id);
        $deleteStmt->execute();

        // Re-insert updated items
        $total_amount = 0;
        $insertStmt = $conn->prepare("
            INSERT INTO import_items (import_ticket_id, product_id, quantity, import_price, total_price)
            VALUES (?, ?, ?, ?, ?)
        ");

        foreach ($import_items as $item) {
            $product_id = intval($item['product_id']);
            $quantity = intval($item['quantity']);
            $import_price = floatval($item['import_price']);
            $total_price = $quantity * $import_price;
            $total_amount += $total_price;

            // Verify product exists
            $checkStmt = $conn->prepare("SELECT id FROM products WHERE id = ?");
            $checkStmt->bind_param("i", $product_id);
            $checkStmt->execute();
            if ($checkStmt->get_result()->num_rows === 0) {
                throw new Exception("Sản phẩm ID {$product_id} không tồn tại");
            }

            $insertStmt->bind_param("iiiid", $ticket_id, $product_id, $quantity, $import_price, $total_price);
            $insertStmt->execute();
        }

        // Update ticket total_amount
        $updateStmt = $conn->prepare("
            UPDATE import_tickets 
            SET total_amount = ?
            WHERE id = ?
        ");
        $updateStmt->bind_param("di", $total_amount, $ticket_id);
        $updateStmt->execute();

        // Commit transaction
        $conn->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Cập nhật phiếu nhập thành công',
            'ticket_id' => $ticket_id,
            'total_amount' => $total_amount
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
