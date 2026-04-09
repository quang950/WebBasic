<?php
/**
 * GET /BackEnd/api/admin/inventory_report.php
 * Báo cáo nhập/xuất sản phẩm trong khoảng thời gian
 */
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../config/db_connect.php';

try {
    // Kiểm tra connection
    if (!$conn) {
        throw new Exception("Database connection failed");
    }
    
    $dateFrom = isset($_GET['dateFrom']) ? trim($_GET['dateFrom']) : date('Y-m-01');
    $dateTo = isset($_GET['dateTo']) ? trim($_GET['dateTo']) : date('Y-m-d');
    $productId = isset($_GET['productId']) ? intval($_GET['productId']) : 0;

    // Convert DATE to DATETIME if needed
    if (strlen($dateFrom) === 10) {
        $dateFrom = $dateFrom . ' 00:00:00';
    }
    if (strlen($dateTo) === 10) {
        $dateTo = $dateTo . ' 23:59:59';
    }

    $sql = "
        SELECT 
            p.id,
            p.name,
            p.brand,
            p.stock as stock_end,
            COALESCE(SUM(CASE WHEN ii.import_ticket_id IS NOT NULL THEN ii.quantity ELSE 0 END), 0) as total_import,
            COALESCE(SUM(CASE WHEN od.order_id IS NOT NULL THEN od.quantity ELSE 0 END), 0) as total_export,
            (COALESCE(SUM(CASE WHEN ii.import_ticket_id IS NOT NULL THEN ii.quantity ELSE 0 END), 0) - 
            COALESCE(SUM(CASE WHEN od.order_id IS NOT NULL THEN od.quantity ELSE 0 END), 0)) as net_change,
            (p.stock - (COALESCE(SUM(CASE WHEN ii.import_ticket_id IS NOT NULL THEN ii.quantity ELSE 0 END), 0) - 
            COALESCE(SUM(CASE WHEN od.order_id IS NOT NULL THEN od.quantity ELSE 0 END), 0))) as stock_begin
        FROM products p
        LEFT JOIN import_items ii ON p.id = ii.product_id 
            AND ii.created_at BETWEEN ? AND ?
        LEFT JOIN order_details od ON p.id = od.product_id 
            AND od.created_at BETWEEN ? AND ?
    ";

    $where = [];
    $params = [$dateFrom, $dateTo, $dateFrom, $dateTo];
    $types = 'ssss';

    if ($productId) {
        $where[] = "p.id = ?";
        $params[] = $productId;
        $types .= 'i';
    }

    $whereStr = !empty($where) ? ' WHERE ' . implode(' AND ', $where) : '';
    $sql .= $whereStr . " GROUP BY p.id ORDER BY total_import DESC, total_export DESC";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare error: " . $conn->error);
    }
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    if (!$stmt->execute()) {
        throw new Exception("Execute error: " . $stmt->error);
    }
    
    $result = $stmt->get_result();

    $report = [];
    while ($row = $result->fetch_assoc()) {
        $report[] = $row;
    }

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $report,
        'filters' => [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'productId' => $productId
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
