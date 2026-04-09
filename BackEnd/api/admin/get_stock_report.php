<?php
/**
 * Admin API: Quản lý Số lượng tồn kho
 * GET /BackEnd/api/admin/get_stock_report.php
 */
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../config/db_connect.php';

// Kiểm tra quyền Admin
$isAdmin = true;
if (isset($_SESSION['user_id']) && isset($_SESSION['is_admin'])) {
    $isAdmin = ($_SESSION['is_admin'] == 1);
}

if (!$isAdmin) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Bạn không có quyền.', 'data' => null]);
    exit;
}

try {
    if (!$conn) {
        throw new Exception("Database connection failed");
    }
    
    $action = $_GET['action'] ?? 'all';
    
    // Get all products with stock info
    if ($action === 'all') {
        $sql = "
            SELECT 
                p.id,
                p.name,
                p.stock,
                p.price,
                p.brand,
                COALESCE(c.name, 'Uncategorized') as category_name,
                CASE 
                    WHEN p.stock <= 2 THEN 'lowStock'
                    WHEN p.stock <= 5 THEN 'normalStock'
                    ELSE 'goodStock'
                END as stock_level
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            ORDER BY p.stock ASC, p.name ASC
            LIMIT 500
        ";
        
        $result = $conn->query($sql);
        if (!$result) {
            throw new Exception("Query error: " . $conn->error);
        }
        
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        
        echo json_encode(['status' => 'success', 'data' => ['products' => $products, 'count' => count($products)]]);
        exit;
    }
    
    // Get low stock products
    if ($action === 'low_stock') {
        $threshold = isset($_GET['threshold']) ? (int)$_GET['threshold'] : 2;
        
        $sql = "
            SELECT 
                p.id,
                p.name,
                p.stock,
                p.brand,
                COALESCE(c.name, 'Uncategorized') as category_name,
                p.price
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.stock <= " . intval($threshold) . "
            ORDER BY p.stock ASC
            LIMIT 100
        ";
        
        $result = $conn->query($sql);
        if (!$result) {
            throw new Exception("Query error: " . $conn->error);
        }
        
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        
        echo json_encode(['status' => 'success', 'data' => ['products' => $products, 'count' => count($products)]]);
        exit;
    }
    
    // Search product stock
    if ($action === 'search_stock') {
        $searchName = isset($_GET['searchName']) ? $conn->real_escape_string($_GET['searchName']) : '';
        
        if (empty($searchName)) {
            throw new Exception("Vui lòng nhập tên sản phẩm");
        }
        
        $sql = "
            SELECT 
                p.id,
                p.name,
                p.stock,
                p.brand,
                COALESCE(c.name, 'Uncategorized') as category_name,
                p.price
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.name LIKE '%$searchName%'
            LIMIT 50
        ";
        
        $result = $conn->query($sql);
        if (!$result) {
            throw new Exception("Query error: " . $conn->error);
        }
        
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        
        echo json_encode(['status' => 'success', 'data' => ['products' => $products, 'count' => count($products)]]);
        exit;
    }
    
    // Historical stock at specific time
    if ($action === 'stock_at_time') {
        $searchName = isset($_GET['searchName']) ? $conn->real_escape_string($_GET['searchName']) : '';
        $targetDate = isset($_GET['targetDate']) ? $_GET['targetDate'] : date('Y-m-d H:i:s');
        
        if (empty($searchName)) {
            throw new Exception("Vui lòng nhập tên sản phẩm");
        }
        
        // Find product
        $sql = "
            SELECT id, name, stock, price
            FROM products
            WHERE name LIKE '%$searchName%'
            LIMIT 1
        ";
        
        $result = $conn->query($sql);
        if (!$result || $result->num_rows === 0) {
            throw new Exception("Không tìm thấy sản phẩm");
        }
        
        $product = $result->fetch_assoc();
        
        // Without detailed history, return current stock as estimate
        // (Real implementation would query stock_history table)
        echo json_encode([
            'status' => 'success',
            'data' => [
                'product_name' => $product['name'],
                'target_date' => $targetDate,
                'stock_at_time' => $product['stock'],
                'current_stock' => $product['stock'],
                'note' => 'Hiển thị tồn kho hiện tại (không có dữ liệu lịch sử)'
            ]
        ]);
        exit;
    }
    
    // Inventory report for date range
    if ($action === 'report_in_out') {
        $searchName = isset($_GET['searchName']) ? $conn->real_escape_string($_GET['searchName']) : '';
        $fromDate = isset($_GET['fromDate']) ? $_GET['fromDate'] : date('Y-m-d');
        $toDate = isset($_GET['toDate']) ? $_GET['toDate'] : date('Y-m-d');
        
        if (empty($searchName)) {
            throw new Exception("Vui lòng nhập tên sản phẩm");
        }
        
        // Find product
        $sql = "
            SELECT id, name, stock, price
            FROM products
            WHERE name LIKE '%$searchName%'
            LIMIT 1
        ";
        
        $result = $conn->query($sql);
        if (!$result || $result->num_rows === 0) {
            throw new Exception("Không tìm thấy sản phẩm");
        }
        
        $product = $result->fetch_assoc();
        
        // Stub response - ideally would query order_items for actual import/export
        echo json_encode([
            'status' => 'success',
            'data' => [
                'product_name' => $product['name'],
                'stock_begin' => $product['stock'],
                'total_import' => 0,
                'total_export' => 0,
                'stock_end' => $product['stock'],
                'note' => 'Báo cáo cơ bản (cần thiết lập sổ nhập xuất chi tiết)'
            ]
        ]);
        exit;
    }
    
    throw new Exception("Action not found: $action");

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage(), 'data' => null]);
}
?>