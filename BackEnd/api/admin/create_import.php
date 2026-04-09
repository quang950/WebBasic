<?php
/**
 * POST /BackEnd/api/admin/create_import.php
 * Tạo phiếu nhập hàng mới
 */
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
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
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ']);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);

    $supplier_name = isset($data['supplier_name']) ? trim($data['supplier_name']) : '';
    $import_date = isset($data['import_date']) ? trim($data['import_date']) : date('Y-m-d');
    $items = isset($data['items']) ? (array)$data['items'] : [];
    $created_by = isset($data['created_by']) ? intval($data['created_by']) : 0;

    if (!$supplier_name || empty($items)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'supplier_name và items bắt buộc']);
        exit;
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Generate ticket number: IMPORT-YYYY-MMDD-XXXXX
        $ticket_prefix = 'IMPORT-' . date('Y-md');
        $stmt = $conn->prepare("
            SELECT COUNT(*) as cnt FROM import_tickets 
            WHERE ticket_number LIKE ?
        ");
        $like_pattern = $ticket_prefix . '%';
        $stmt->bind_param("s", $like_pattern);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_assoc()['cnt'];
        $ticket_number = $ticket_prefix . '-' . str_pad($count + 1, 5, '0', STR_PAD_LEFT);

        // Create import ticket
        $stmt = $conn->prepare("
            INSERT INTO import_tickets (ticket_number, supplier_name, import_date, created_by)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("sssi", $ticket_number, $supplier_name, $import_date, $created_by);
        $stmt->execute();
        $ticket_id = $conn->insert_id;

        $total_amount = 0;

        // Add items
        foreach ($items as $item) {
            $product_id = isset($item['product_id']) ? intval($item['product_id']) : 0;
            $quantity = isset($item['quantity']) ? intval($item['quantity']) : 0;
            $import_price = isset($item['import_price']) ? floatval($item['import_price']) : 0;

            if (!$product_id || $quantity <= 0 || $import_price <= 0) {
                throw new Exception('Dữ liệu sản phẩm không hợp lệ');
            }

            // Check product exists
            $pstmt = $conn->prepare("SELECT id FROM products WHERE id = ?");
            $pstmt->bind_param("i", $product_id);
            $pstmt->execute();
            if ($pstmt->get_result()->num_rows === 0) {
                throw new Exception("Sản phẩm ID $product_id không tồn tại");
            }

            $item_total = $quantity * $import_price;
            $total_amount += $item_total;

            // Add import item
            $istmt = $conn->prepare("
                INSERT INTO import_items (import_ticket_id, product_id, quantity, import_price, total_price)
                VALUES (?, ?, ?, ?, ?)
            ");
            $istmt->bind_param("iiidi", $ticket_id, $product_id, $quantity, $import_price, $item_total);
            $istmt->execute();
        }

        // Update ticket total amount
        $ustmt = $conn->prepare("UPDATE import_tickets SET total_amount = ? WHERE id = ?");
        $ustmt->bind_param("di", $total_amount, $ticket_id);
        $ustmt->execute();

        // Commit
        $conn->commit();

        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Tạo phiếu nhập hàng thành công',
            'data' => [
                'ticket_id' => $ticket_id,
                'ticket_number' => $ticket_number,
                'total_amount' => $total_amount,
                'items_count' => count($items)
            ]
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
