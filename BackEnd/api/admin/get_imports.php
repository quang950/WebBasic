<?php
/**
 * GET /BackEnd/api/admin/get_imports.php
 * Lấy danh sách phiếu nhập hàng
 */
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
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
    $status = isset($_GET['status']) ? trim($_GET['status']) : ''; // 'pending' or 'completed'
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;

    $where = '';
    $params = [];
    $types = '';

    if (!empty($status)) {
        $where = "WHERE completed_at IS " . ($status === 'completed' ? 'NOT' : '') . " NULL";
    }

    // Count total
    $countSql = "SELECT COUNT(*) as total FROM import_tickets $where";
    $countStmt = $conn->prepare($countSql);
    $countResult = $countStmt->get_result();
    $totalRows = $countResult->fetch_assoc()['total'];
    $totalPages = ceil($totalRows / $limit);

    // Get paginated data
    $offset = ($page - 1) * $limit;

    $sql = "
        SELECT 
            t.id,
            t.ticket_number,
            t.supplier_name,
            t.import_date,
            t.total_amount,
            t.created_by,
            u.email as created_by_email,
            t.created_at,
            t.completed_at,
            COUNT(i.id) as items_count
        FROM import_tickets t
        LEFT JOIN users u ON t.created_by = u.id
        LEFT JOIN import_items i ON t.id = i.import_ticket_id
        $where
        GROUP BY t.id
        ORDER BY t.created_at DESC
        LIMIT ? OFFSET ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    $imports = [];
    while ($row = $result->fetch_assoc()) {
        // Get items
        $istmt = $conn->prepare("
            SELECT i.id, i.product_id, p.name, i.quantity, i.import_price, i.total_price
            FROM import_items i
            LEFT JOIN products p ON i.product_id = p.id
            WHERE i.import_ticket_id = ?
        ");
        $istmt->bind_param("i", $row['id']);
        $istmt->execute();
        $iresult = $istmt->get_result();

        $items = [];
        while ($irow = $iresult->fetch_assoc()) {
            $items[] = $irow;
        }

        $row['items'] = $items;
        $imports[] = $row;
    }

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $imports,
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total' => $totalRows,
            'pages' => $totalPages
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi server: ' . $e->getMessage()
    ]);
}
?>
