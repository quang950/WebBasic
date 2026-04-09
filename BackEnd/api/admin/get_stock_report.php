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
        $searchName = isset($_GET['searchName']) ? trim($_GET['searchName']) : '';
        
        if (empty($searchName)) {
            throw new Exception("Vui lòng nhập tên sản phẩm");
        }
        
        $searchPattern = '%' . $searchName . '%';
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
            WHERE p.name LIKE ? OR p.brand LIKE ?
            ORDER BY 
                CASE 
                    WHEN p.name = ? OR p.brand = ? THEN 0
                    WHEN p.name LIKE CONCAT(?, '%') OR p.brand LIKE CONCAT(?, '%') THEN 1
                    ELSE 2
                END,
                p.name ASC
            LIMIT 50
        ";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Lỗi chuẩn bị câu lệnh: " . $conn->error);
        }
        
        $stmt->bind_param("ssssss",
            $searchPattern, $searchPattern,
            $searchName, $searchName,
            $searchName, $searchName
        );
        $stmt->execute();
        $result = $stmt->get_result();
        
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
        
        // Find product - prioritize exact/better matches
        $sql = "
            SELECT id, name, stock, price, brand
            FROM products
            WHERE name LIKE ? OR brand LIKE ?
            ORDER BY 
                CASE 
                    WHEN name = ? OR brand = ? THEN 0
                    WHEN name LIKE CONCAT(?, '%') OR brand LIKE CONCAT(?, '%') THEN 1
                    WHEN name LIKE CONCAT('%', ?) OR brand LIKE CONCAT('%', ?) THEN 2
                    ELSE 3
                END,
                name ASC
            LIMIT 1
        ";
        
        $searchPattern = '%' . $searchName . '%';
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Lỗi chuẩn bị câu lệnh: " . $conn->error);
        }
        
        $stmt->bind_param("ssssssss",
            $searchPattern, $searchPattern,
            $searchName, $searchName,
            $searchName, $searchName,
            $searchName, $searchName
        );
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception("Không tìm thấy sản phẩm phù hợp với '{$searchName}'");
        }

        $product = $result->fetch_assoc();
        $product_id = $product['id'];
        $current_stock = $product['stock'];
        
        // Calculate stock at target time by summing all changes before/at that time
        // Query 1: Get all history changes UP TO target time
        $histSql = "
            SELECT 
                COUNT(*) as history_count,
                COALESCE(SUM(CASE WHEN type = 'import' THEN quantity ELSE 0 END), 0) as total_imported,
                COALESCE(SUM(CASE WHEN type = 'sale' THEN quantity ELSE 0 END), 0) as total_sold,
                COALESCE(SUM(CASE WHEN type = 'adjustment' THEN quantity ELSE 0 END), 0) as total_adjusted
            FROM stock_history
            WHERE product_id = ? AND created_at <= ?
        ";
        
        $stmt = $conn->prepare($histSql);
        if (!$stmt) {
            throw new Exception("Lỗi chuẩn bị câu lệnh: " . $conn->error);
        }
        
        $stmt->bind_param("is", $product_id, $targetDate);
        $stmt->execute();
        $histResult = $stmt->get_result();
        $histData = $histResult->fetch_assoc();

        // Query 2: Get changes AFTER target time (to deduct from current stock)
        $afterSql = "
            SELECT 
                COALESCE(SUM(CASE WHEN type = 'import' THEN quantity ELSE 0 END), 0) as total_imported_after,
                COALESCE(SUM(CASE WHEN type = 'sale' THEN quantity ELSE 0 END), 0) as total_sold_after,
                COALESCE(SUM(CASE WHEN type = 'adjustment' THEN quantity ELSE 0 END), 0) as total_adjusted_after
            FROM stock_history
            WHERE product_id = ? AND created_at > ?
        ";
        
        $stmt2 = $conn->prepare($afterSql);
        $stmt2->bind_param("is", $product_id, $targetDate);
        $stmt2->execute();
        $afterResult = $stmt2->get_result();
        $afterData = $afterResult->fetch_assoc();
        
        // Calculate: Current stock - changes that happened after target time
        // stock_at_time = current + sold_after - imported_after - adjusted_after
        $net_change_after = $afterData['total_imported_after'] - $afterData['total_sold_after'] + $afterData['total_adjusted_after'];
        $stock_at_time = $current_stock - $net_change_after;
        
        // Ensure non-negative
        if ($stock_at_time < 0) {
            $stock_at_time = 0;
        }
        
        echo json_encode([
            'status' => 'success',
            'data' => [
                'product_name' => $product['name'],
                'target_date' => $targetDate,
                'stock_at_time' => $stock_at_time,
                'current_stock' => $current_stock,
                'debug' => [
                    'product_id' => $product_id,
                    'history_count_before' => $histData['history_count'],
                    'total_imported_before' => $histData['total_imported'],
                    'total_sold_before' => $histData['total_sold'],
                    'total_adjusted_before' => $histData['total_adjusted'],
                    'net_change_after_target' => $net_change_after,
                    'calculation' => "current_stock($current_stock) - net_change_after($net_change_after) = $stock_at_time"
                ]
            ]
        ]);
        exit;
    }
    
    // Inventory report for date range
    if ($action === 'report_in_out') {
        $searchName = isset($_GET['searchName']) ? trim($_GET['searchName']) : '';
        $fromDate = isset($_GET['fromDate']) ? $_GET['fromDate'] : date('Y-m-d');
        $toDate = isset($_GET['toDate']) ? $_GET['toDate'] : date('Y-m-d');
        
        // Log input parameters
        error_log("=== report_in_out START ===");
        error_log("searchName: '{$searchName}'");
        error_log("fromDate: '{$fromDate}'");
        error_log("toDate: '{$toDate}'");
        
        if (empty($searchName)) {
            throw new Exception("Vui lòng nhập tên sản phẩm");
        }
        
        // Find product - search in both name and brand with exact match priority
        $searchPattern = '%' . $searchName . '%';
        $sql = "
            SELECT id, name, stock, price, brand
            FROM products
            WHERE name LIKE ? OR brand LIKE ?
            ORDER BY 
                CASE 
                    WHEN name = ? OR brand = ? THEN 0
                    WHEN name LIKE CONCAT(?, '%') OR brand LIKE CONCAT(?, '%') THEN 1
                    WHEN name LIKE CONCAT('%', ?) OR brand LIKE CONCAT('%', ?) THEN 2
                    ELSE 3
                END,
                name ASC
            LIMIT 1
        ";
        
        error_log("Product search SQL: {$sql}");
        error_log("Search pattern: '{$searchPattern}'");
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare error: " . $conn->error);
            throw new Exception("Lỗi chuẩn bị câu lệnh: " . $conn->error);
        }
        
        $stmt->bind_param("ssssssss", 
            $searchPattern, $searchPattern,
            $searchName, $searchName,
            $searchName, $searchName,
            $searchName, $searchName
        );
        
        if (!$stmt->execute()) {
            error_log("Execute error: " . $stmt->error);
            throw new Exception("Execute error: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        error_log("Product search result rows: " . $result->num_rows);
        
        if ($result->num_rows === 0) {
            error_log("No product found for: '{$searchName}'");
            throw new Exception("Không tìm thấy sản phẩm phù hợp với '{$searchName}'");
        }
        
        $product = $result->fetch_assoc();
        $product_id = $product['id'];
        $current_stock = $product['stock'];
        
        error_log("Found product - ID: {$product_id}, Name: {$product['name']}, Current Stock: {$current_stock}, Brand: {$product['brand']}");
        
        // Query stock_history for imports and exports in date range
        $histSql = "
            SELECT 
                COALESCE(SUM(CASE WHEN type = 'import' THEN quantity ELSE 0 END), 0) as total_imported,
                COALESCE(SUM(CASE WHEN type = 'sale' THEN quantity ELSE 0 END), 0) as total_sold,
                COALESCE(SUM(CASE WHEN type = 'adjustment' THEN quantity ELSE 0 END), 0) as total_adjusted
            FROM stock_history
            WHERE product_id = ? 
            AND DATE(created_at) >= DATE(?)
            AND DATE(created_at) <= DATE(?)
        ";
        
        error_log("History SQL: {$histSql}");
        error_log("History params - Product ID: {$product_id}, From: {$fromDate}, To: {$toDate}");
        
        $stmt = $conn->prepare($histSql);
        if (!$stmt) {
            error_log("History prepare error: " . $conn->error);
            throw new Exception("Lỗi chuẩn bị câu lệnh: " . $conn->error);
        }
        
        $stmt->bind_param("iss", $product_id, $fromDate, $toDate);
        if (!$stmt->execute()) {
            error_log("History execute error: " . $stmt->error);
            throw new Exception("Execute error in history query: " . $stmt->error);
        }
        
        $histResult = $stmt->get_result();
        $histData = $histResult->fetch_assoc();
        
        error_log("History query result: " . json_encode($histData));
        
        if (!$histData) {
            $histData = [
                'total_imported' => 0,
                'total_sold' => 0,
                'total_adjusted' => 0
            ];
            error_log("No history data found, using zeros");
        }
        
        // stock_begin = current_stock - net changes in period
        $net_change_in_period = $histData['total_imported'] - $histData['total_sold'] + $histData['total_adjusted'];
        $stock_begin = $current_stock - $net_change_in_period;
        $stock_end = $current_stock;
        
        error_log("Calculations - Net change: {$net_change_in_period}, Stock begin: {$stock_begin}, Stock end: {$stock_end}");
        
        if ($stock_begin < 0) $stock_begin = 0;
        
        error_log("=== report_in_out SUCCESS ===");
        
        echo json_encode([
            'status' => 'success',
            'data' => [
                'product_name' => $product['name'],
                'product_id' => $product_id,
                'period_from' => $fromDate,
                'period_to' => $toDate,
                'stock_begin' => intval($stock_begin),
                'total_import' => intval($histData['total_imported']),
                'total_sale' => intval($histData['total_sold']),
                'total_adjustment' => intval($histData['total_adjusted']),
                'stock_end' => intval($stock_end),
                'current_stock' => intval($current_stock),
                'note' => 'Báo cáo từ lịch sử nhập/xuất'
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