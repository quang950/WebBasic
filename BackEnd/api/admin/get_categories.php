<?php
/**
 * Admin API: Get all categories
 * GET /BackEnd/api/admin/get_categories.php
 */
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/db_connect.php';

try {
    if (!$conn) {
        throw new Exception('Database connection failed: ' . ($dbError ?? 'Unknown error'));
    }
    
    $search = $_GET['search'] ?? '';
    $status = $_GET['status'] ?? '';
    
    // Build query
    $sql = "
        SELECT 
            c.id, 
            c.name, 
            c.description, 
            c.status,
            c.created_at,
            c.updated_at,
            COUNT(p.id) as product_count
        FROM categories c
        LEFT JOIN products p ON p.category_id = c.id
        WHERE 1=1
    ";
    
    $bind_types = '';
    $bind_params = [];
    
    if (!empty($search)) {
        $search_param = "%$search%";
        $sql .= " AND c.name LIKE ?";
        $bind_types .= 's';
        $bind_params[] = &$search_param;
    }
    
    if ($status !== '') {
        $status_param = (int)$status;
        $sql .= " AND c.status = ?";
        $bind_types .= 'i';
        $bind_params[] = &$status_param;
    }
    
    $sql .= " GROUP BY c.id ORDER BY c.name ASC";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }
    
    // Bind parameters if any
    if (!empty($bind_params)) {
        $stmt->bind_param($bind_types, ...$bind_params);
    }
    
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    if (!$result) {
        throw new Exception('Get result failed: ' . $stmt->error);
    }
    
    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $row['status_text'] = $row['status'] == 1 ? 'Đang hiển thị' : 'Đang ẩn';
        $categories[] = $row;
    }
    
    $stmt->close();
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'categories' => $categories,
        'total' => count($categories)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage(),
        'debug' => [
            'db_connected' => !empty($conn),
            'db_error' => $dbError ?? null
        ]
    ]);
}
?>
