<?php
/**
 * Admin API: Get all categories
 * GET /BackEnd/api/admin/get_categories.php
 */
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/db_connect.php';

try {
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
    
    if (!empty($search)) {
        $search = "%$search%";
        $sql .= " AND c.name LIKE ?";
    }
    
    if ($status !== '') {
        $sql .= " AND c.status = ?";
    }
    
    $sql .= " GROUP BY c.id ORDER BY c.name ASC";
    
    $stmt = $conn->prepare($sql);
    
    $bind_types = '';
    $bind_values = [];
    
    if (!empty($search) && $status !== '') {
        $bind_types = 'si';
        $bind_values = [&$search, &$status];
        $stmt->bind_param($bind_types, ...$bind_values);
    } elseif (!empty($search)) {
        $stmt->bind_param('s', $search);
    } elseif ($status !== '') {
        $stmt->bind_param('i', $status);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $row['status_text'] = $row['status'] == 1 ? 'Đang hiển thị' : 'Đang ẩn';
        $categories[] = $row;
    }
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $categories,
        'total' => count($categories)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage()
    ]);
}
?>
