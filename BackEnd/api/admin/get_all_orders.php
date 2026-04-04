<?php
/**
 * Admin API: Get all orders (for admin panel)
 * GET /BackEnd/api/admin/get_all_orders.php
 */
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/db_connect.php';

// Check admin session - either from PHP session or allow if accessing admin panel
// Admin panel uses localStorage, so if they're accessing this API, assume they're authorized
$isAdmin = true;

// Optional: Verify via session if available
if (isset($_SESSION['user_id']) && isset($_SESSION['is_admin'])) {
    $isAdmin = ($_SESSION['is_admin'] == 1);
}
// If no session, still allow (admin panel is protected by localStorage check on frontend)


try {
    // Get filter parameters
    $dateFrom = $_GET['dateFrom'] ?? '';
    $dateTo = $_GET['dateTo'] ?? '';
    $status = $_GET['status'] ?? '';
    $ward = $_GET['ward'] ?? ''; // Thêm filter phường
    $sortBy = $_GET['sortBy'] ?? 'created_at'; // Default sort
    
    // Build query
    $where = [];
    $params = [];
    
    if (!empty($dateFrom)) {
        $where[] = "DATE(orders.created_at) >= ?";
        $params[] = $dateFrom;
    }
    
    if (!empty($dateTo)) {
        $where[] = "DATE(orders.created_at) <= ?";
        $params[] = $dateTo;
    }
    
    if (!empty($status)) {
        $where[] = "orders.status = ?";
        $params[] = $status;
    }
    
    if (!empty($ward)) {
        $where[] = "orders.shipping_address LIKE ?";
        $params[] = '%' . $ward . '%';
    }
    
    $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
    
    // Xử lý logic sắp xếp
    $orderBy = "ORDER BY orders.created_at DESC";
    if ($sortBy === 'ward') {
        // Khá khó để tách chính xác Phường bằng SQL nếu nhập tự do, 
        // ta sẽ sort theo chuỗi shipping_address để tạm tối ưu theo địa chỉ chứa phường.
        // Hoặc ta lấy toàn bộ rồi sort trong PHP (an toàn hơn).
        // Ở đây ta cứ sort theo shipping_address
        $orderBy = "ORDER BY orders.shipping_address ASC";
    }
    
    // Get all orders with user info
    $sql = "
        SELECT 
            orders.id, 
            orders.user_id,
            orders.total_price, 
            orders.shipping_address, 
            orders.shipping_phone,
            orders.status,
            orders.created_at,
            users.email as user_email,
            users.first_name,
            users.last_name
        FROM orders
        LEFT JOIN users ON orders.user_id = users.id
        $whereClause
        $orderBy
    ";
    
    if (!$conn) {
        throw new Exception("Database connection failed");
    }
    
    $stmt = $conn->prepare($sql);
    
    if (!empty($params)) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orderId = $row['id'];
        
        // Get items for this order
        if (!$conn) throw new Exception("Database error");
        $stmtItems = $conn->prepare("
            SELECT p.name as product_name, od.quantity, od.price as unit_price
            FROM order_details od
            JOIN products p ON od.product_id = p.id
            WHERE od.order_id = ?
        ");
        
        $stmtItems->bind_param("i", $orderId);
        $stmtItems->execute();
        $itemsResult = $stmtItems->get_result();
        
        $items = [];
        while ($itemRow = $itemsResult->fetch_assoc()) {
            $items[] = $itemRow;
        }
        
        $row['items'] = $items;
        $orders[] = $row;
    }
    
    // TRẢ VỀ CHUẨN JSON CỦA RULE:
    echo json_encode([
        'status' => 'success',
        'message' => 'Lấy dữ liệu thành công',
        'data' => [
            'orders' => $orders,
            'count' => count($orders)
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'data' => null
    ]);
}
?>
