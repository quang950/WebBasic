<?php
/**
 * GET /BackEnd/api/admin/get_orders.php
 * Lấy danh sách đơn hàng với filter & sort
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
    // Get filters
    $status = isset($_GET['status']) ? trim($_GET['status']) : '';
    $dateFrom = isset($_GET['dateFrom']) ? trim($_GET['dateFrom']) : '';
    $dateTo = isset($_GET['dateTo']) ? trim($_GET['dateTo']) : '';
    $province = isset($_GET['province']) ? trim($_GET['province']) : '';
    $district = isset($_GET['district']) ? trim($_GET['district']) : '';
    $sortBy = isset($_GET['sortBy']) ? trim($_GET['sortBy']) : 'created_at';
    $sortOrder = isset($_GET['sortOrder']) ? trim($_GET['sortOrder']) : 'DESC';
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;

    // Validate sort
    $allowedSort = ['created_at', 'total_price', 'status', 'shipping_district', 'shipping_province'];
    if (!in_array($sortBy, $allowedSort)) $sortBy = 'created_at';

    $sortOrder = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';

    // Build query
    $where = [];
    $params = [];
    $types = '';

    if (!empty($status)) {
        $where[] = "orders.status = ?";
        $params[] = $status;
        $types .= 's';
    }

    if (!empty($dateFrom)) {
        $where[] = "DATE(orders.created_at) >= ?";
        $params[] = $dateFrom;
        $types .= 's';
    }

    if (!empty($dateTo)) {
        $where[] = "DATE(orders.created_at) <= ?";
        $params[] = $dateTo;
        $types .= 's';
    }

    if (!empty($province)) {
        $where[] = "orders.shipping_province LIKE ?";
        $params[] = "%$province%";
        $types .= 's';
    }

    if (!empty($district)) {
        $where[] = "orders.shipping_district LIKE ?";
        $params[] = "%$district%";
        $types .= 's';
    }

    $whereStr = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

    // Count total
    $countSql = "SELECT COUNT(*) as total FROM orders $whereStr";
    $countStmt = $conn->prepare($countSql);
    
    if (!empty($params)) {
        $countStmt->bind_param($types, ...$params);
    }
    
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    $totalRows = $countResult->fetch_assoc()['total'];
    $totalPages = ceil($totalRows / $limit);

    // Get paginated data
    $offset = ($page - 1) * $limit;

    $sql = "
        SELECT 
            o.id, 
            o.user_id,
            u.first_name, 
            u.last_name,
            u.email,
            o.total_price, 
            o.shipping_address,
            o.shipping_district,
            o.shipping_province,
            o.shipping_phone,
            o.payment_method,
            o.status,
            o.created_at,
            COUNT(od.id) as item_count
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        LEFT JOIN order_details od ON o.id = od.order_id
        $whereStr
        GROUP BY o.id
        ORDER BY o.$sortBy $sortOrder
        LIMIT ? OFFSET ?
    ";

    $stmt = $conn->prepare($sql);
    
    if (!empty($params)) {
        $params[] = $limit;
        $params[] = $offset;
        $types .= 'ii';
        $stmt->bind_param($types, ...$params);
    } else {
        $stmt->bind_param('ii', $limit, $offset);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $orders = [];
    while ($row = $result->fetch_assoc()) {
        // Get order items
        $itemStmt = $conn->prepare("
            SELECT od.id, od.product_id, p.name, od.quantity, od.price
            FROM order_details od
            LEFT JOIN products p ON od.product_id = p.id
            WHERE od.order_id = ?
        ");
        $itemStmt->bind_param("i", $row['id']);
        $itemStmt->execute();
        $itemResult = $itemStmt->get_result();

        $items = [];
        while ($item = $itemResult->fetch_assoc()) {
            $items[] = $item;
        }

        $row['items'] = $items;
        $orders[] = $row;
    }

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $orders,
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total' => $totalRows,
            'pages' => $totalPages
        ],
        'filters' => [
            'status' => $status,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'province' => $province,
            'district' => $district
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
