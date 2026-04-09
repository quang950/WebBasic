<?php
/**
 * Admin API: Get all customers
 * GET /BackEnd/api/admin/get_all_customers.php
 */
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/db_connect.php';

try {
    if (!$conn) {
        throw new Exception('Database connection failed');
    }
    
    // Get filter parameters
    $search = $_GET['search'] ?? '';
    $status = $_GET['status'] ?? ''; // 'active' or 'locked'
    
    // Build query
    $where = [];
    $params = [];
    
    // Don't include admin users in customer list
    $where[] = "users.is_admin = 0";
    
    if (!empty($search)) {
        $where[] = "(users.first_name LIKE ? OR users.last_name LIKE ? OR users.email LIKE ? OR users.phone LIKE ?)";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "WHERE is_admin = 0";
    
    // Get all customers with order/registration info
    $sql = "
        SELECT 
            users.id,
            users.first_name,
            users.last_name,
            users.email,
            users.phone,
            users.province,
            users.created_at,
            users.locked,
            COUNT(DISTINCT orders.id) as order_count,
            SUM(orders.total_price) as total_spent
        FROM users
        LEFT JOIN orders ON users.id = orders.user_id
        $whereClause
        GROUP BY users.id
        ORDER BY users.created_at DESC
    ";
    
    $stmt = $conn->prepare($sql);
    
    if (!empty($params)) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $customers = [];
    while ($row = $result->fetch_assoc()) {
        $customers[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'customers' => $customers,
        'count' => count($customers)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
