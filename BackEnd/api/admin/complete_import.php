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

    if (!$ticket_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ticket_id bắt buộc']);
        exit;
    }

    // Check ticket exists
    $stmt = $conn->prepare("SELECT id, completed_at, import_date FROM import_tickets WHERE id = ?");
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
            // Get current stock and cost_price BEFORE update (BÌNH QUÂN calculation)
            $pstmt = $conn->prepare("SELECT stock, cost_price FROM products WHERE id = ?");
            $pstmt->bind_param("i", $item['product_id']);
            $pstmt->execute();
            $presult = $pstmt->get_result();
            $previous_stock = 0;
            $current_cost_price = 0;
            if ($presult->num_rows > 0) {
                $prow = $presult->fetch_assoc();
                $previous_stock = intval($prow['stock']);
                $current_cost_price = floatval($prow['cost_price']);
            }

            // Calculate new cost_price using BÌNH QUÂN (weighted average)
            // New cost = (current_stock × current_cost + new_qty × import_price) / (current_stock + new_qty)
            $new_quantity = intval($item['quantity']);
            $import_price = floatval($item['import_price']);
            $total_stock = $previous_stock + $new_quantity;
            $new_cost_price = $total_stock > 0 
                ? ($previous_stock * $current_cost_price + $new_quantity * $import_price) / $total_stock
                : $import_price;

            // Update product stock and cost_price
            $ustmt = $conn->prepare("
                UPDATE products 
                SET stock = stock + ?, 
                    cost_price = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");
            $ustmt->bind_param("idi", $new_quantity, $new_cost_price, $item['product_id']);
            $ustmt->execute();

            // Calculate new stock after update
            $new_stock = $previous_stock + intval($item['quantity']);

            // Record stock history with import_date as the transaction date
            $import_date = $ticket['import_date']; // This is DATE format (YYYY-MM-DD)
            // Convert DATE to DATETIME by appending 00:00:00
            $import_datetime = $import_date . ' 00:00:00';
            
            $hstmt = $conn->prepare("
                INSERT INTO stock_history (product_id, type, quantity, reason, previous_stock, new_stock, created_at)
                VALUES (?, 'import', ?, ?, ?, ?, STR_TO_DATE(?, '%Y-%m-%d %H:%i:%s'))
            ");
            $reason = "Import Ticket #$ticket_id";
            $product_id = intval($item['product_id']);
            $quantity = intval($item['quantity']);
            
            $hstmt->bind_param("iisiiss", $product_id, $quantity, $reason, $previous_stock, $new_stock, $import_datetime);
            
            // Debug: Log the query and values
            error_log("Stock History Insert - Product: {$product_id}, Qty: {$quantity}, Prev: {$previous_stock}, New: {$new_stock}, Date: {$import_datetime}");
            
            if (!$hstmt->execute()) {
                error_log("Stock History Insert Failed: " . $hstmt->error);
                throw new Exception("Lỗi INSERT stock_history: " . $hstmt->error);
            }
            
            error_log("Stock History Insert Success for Product {$product_id}");
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
